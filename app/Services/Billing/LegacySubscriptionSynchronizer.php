<?php

namespace App\Services\Billing;

use App\Models\Tenant;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\DB;
use Revoltify\Subscriptionify\Enums\FeatureType;
use Revoltify\Subscriptionify\Enums\SubscriptionStatus;
use Revoltify\Subscriptionify\Models\Feature;
use Revoltify\Subscriptionify\Models\Plan;
use Revoltify\Subscriptionify\Models\Subscription as PlanSubscription;

class LegacySubscriptionSynchronizer
{
    public function sync(Tenant $tenant): void
    {
        DB::transaction(function () use ($tenant): void {
            $tenant = Tenant::query()
                ->whereKey($tenant->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $tenant->loadMissing(['customer', 'subscriptions']);

            if ($tenant->has_gratis) {
                $this->saveSubscription(
                    $tenant,
                    $this->noCostPlan(),
                    SubscriptionStatus::Active,
                    now(),
                );

                return;
            }

            $sparkSubscription = $tenant->subscription();

            if ($sparkSubscription?->valid()) {
                $sparkPlan = $tenant->sparkPlan();
                $plan = $sparkPlan === null
                    ? $this->unmappedLegacyPlan($sparkSubscription->paddle_plan)
                    : $this->legacyPlan($sparkPlan);
                $status = match (true) {
                    $sparkSubscription->onTrial() => SubscriptionStatus::Trialing,
                    $sparkSubscription->pastDue() => SubscriptionStatus::PastDue,
                    $sparkSubscription->cancelled() || $sparkSubscription->paused() => SubscriptionStatus::Cancelled,
                    default => SubscriptionStatus::Active,
                };
                $endsAt = $sparkSubscription->paused()
                    ? $sparkSubscription->paused_from
                    : $sparkSubscription->ends_at;
                $cancelledAt = $sparkSubscription->cancelled() || $sparkSubscription->paused()
                    ? now()
                    : null;

                $this->saveSubscription(
                    $tenant,
                    $plan,
                    $status,
                    $sparkSubscription->created_at ?? now(),
                    $endsAt,
                    $sparkSubscription->trial_ends_at,
                    $cancelledAt,
                );

                return;
            }

            if ($tenant->onGenericTrial()) {
                $trialEndsAt = $tenant->trialEndsAt();

                $this->saveSubscription(
                    $tenant,
                    $this->trialPlan(),
                    SubscriptionStatus::Trialing,
                    $tenant->customer?->created_at ?? now(),
                    $trialEndsAt,
                    $trialEndsAt,
                );

                return;
            }

            if ($tenant->customer?->trial_ends_at !== null) {
                $trialEndsAt = $tenant->customer->trial_ends_at;

                $this->saveSubscription(
                    $tenant,
                    $this->trialPlan(),
                    SubscriptionStatus::Expired,
                    $tenant->customer->created_at ?? now(),
                    $trialEndsAt,
                    $trialEndsAt,
                );

                return;
            }

            $this->expireSubscriptions($tenant);
        });
    }

    private function saveSubscription(
        Tenant $tenant,
        Plan $plan,
        SubscriptionStatus $status,
        CarbonInterface $startsAt,
        ?CarbonInterface $endsAt = null,
        ?CarbonInterface $trialEndsAt = null,
        ?CarbonInterface $cancelledAt = null,
    ): void {
        $subscriptions = $tenant->planSubscriptions();
        $current = $subscriptions->latest('id')->lockForUpdate()->first();
        $attributes = [
            'plan_id' => $plan->getKey(),
            'status' => $status,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'trial_ends_at' => $trialEndsAt,
            'cancelled_at' => $cancelledAt,
        ];

        if ($current instanceof PlanSubscription) {
            if ($current->plan_id === $plan->getKey() && $current->getStatus() === $status) {
                $attributes['starts_at'] = $current->getStartsAt();
            }

            $current->update($attributes);
        } else {
            $current = $subscriptions->create($attributes);
        }

        $subscriptions->whereKeyNot($current->getKey())
            ->whereIn('status', [
                SubscriptionStatus::Active,
                SubscriptionStatus::Trialing,
                SubscriptionStatus::PastDue,
                SubscriptionStatus::Cancelled,
            ])
            ->update([
                'status' => SubscriptionStatus::Expired,
                'ends_at' => now(),
            ]);
    }

    private function expireSubscriptions(Tenant $tenant): void
    {
        $tenant->planSubscriptions()
            ->whereIn('status', [
                SubscriptionStatus::Active,
                SubscriptionStatus::Trialing,
                SubscriptionStatus::PastDue,
                SubscriptionStatus::Cancelled,
            ])
            ->update([
                'status' => SubscriptionStatus::Expired,
                'cancelled_at' => now(),
                'ends_at' => now(),
            ]);
    }

    private function noCostPlan(): Plan
    {
        return $this->plan('has-gratis', 'No-cost access', true);
    }

    private function trialPlan(): Plan
    {
        return $this->plan('legacy-trial', 'Spark trial');
    }

    private function legacyPlan(object $sparkPlan): Plan
    {
        $plan = $this->plan(
            'legacy-spark-'.$sparkPlan->id,
            $sparkPlan->name,
            false,
            $sparkPlan->shortDescription,
        );
        $options = $sparkPlan->options;
        $featureValues = collect([
            'active-user-quota' => $options['activeUserQuota'] ?? null,
            'active-user-quota-buffer' => $options['activeUserQuotaBuffer'] ?? null,
            'active-user-grace-period-days' => $options['activeUserGracePeriodDays'] ?? null,
        ])
            ->reject(fn (mixed $value): bool => $value === null)
            ->mapWithKeys(function (mixed $value, string $slug): array {
                $feature = Feature::query()->firstOrCreate([
                    'slug' => $slug,
                ], [
                    'name' => str($slug)->replace('-', ' ')->title()->toString(),
                    'type' => FeatureType::Limit,
                ]);

                return [$feature->getKey() => ['value' => (string) $value]];
            });

        $plan->features()->sync($featureValues->all());

        return $plan;
    }

    private function unmappedLegacyPlan(string|int|null $paddlePlanId): Plan
    {
        $identifier = $paddlePlanId === null ? 'unknown' : (string) $paddlePlanId;

        return $this->plan(
            'legacy-spark-'.$identifier,
            'Legacy Spark plan '.$identifier,
        );
    }

    private function plan(string $slug, string $name, bool $isFree = false, ?string $description = null): Plan
    {
        $plan = Plan::query()->updateOrCreate(['slug' => $slug], [
            'name' => $name,
            'description' => $description,
            'is_free' => $isFree,
            'is_active' => true,
            'trial_days' => 0,
            'billing_period' => 1,
            'billing_interval' => 'year',
            'grace_days' => 0,
        ]);

        if ($isFree || $slug === 'legacy-trial') {
            $plan->features()->sync([]);
        }

        return $plan;
    }
}
