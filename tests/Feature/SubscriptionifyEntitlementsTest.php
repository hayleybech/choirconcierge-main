<?php

use App\Models\Tenant;
use App\Models\Membership;
use App\Services\Billing\LegacyEntitlementSynchronizer;
use Revoltify\Subscriptionify\Models\Plan;
use Revoltify\Subscriptionify\Models\Subscription as EntitlementSubscription;
use Revoltify\Subscriptionify\Enums\SubscriptionStatus;

test('tenant access is decided by its confirmed Subscriptionify entitlement', function () {
    $tenant = Tenant::factory()->create([
        'timezone' => 'Australia/Perth',
    ]);
    $tenant->customer()->update(['trial_ends_at' => null]);

    $plan = Plan::query()->create([
        'name' => 'Legacy Small',
        'slug' => 'legacy-small',
    ]);

    EntitlementSubscription::query()->create([
        'subscribable_type' => $tenant->getMorphClass(),
        'subscribable_id' => $tenant->getKey(),
        'plan_id' => $plan->getKey(),
        'status' => 'active',
        'starts_at' => now(),
    ]);

    expect($tenant->fresh()->billing_status['valid'])->toBeTrue();
});

test('a Spark-owned tenant receives a mirrored entitlement with the configured tier limits', function (int $planId, ?int $quota): void {
    $tenant = Tenant::factory()
        ->withSubscription($planId)
        ->create(['timezone' => 'Australia/Perth']);
    $entitlement = $tenant->entitlementSubscriptions()->with('plan.features')->latest('id')->firstOrFail();
    $features = $entitlement->plan->features->mapWithKeys(
        fn ($feature): array => [$feature->slug => (int) $feature->pivot->value],
    );

    expect($entitlement->getStatus())->toBe(SubscriptionStatus::Active)
        ->and($entitlement->plan->getSlug())->toStartWith('legacy-spark-')
        ->and($features->get('active-user-quota'))->toBe($quota)
        ->and($features->get('active-user-quota-buffer'))->toBe($quota === null ? null : 5)
        ->and($features->get('active-user-grace-period-days'))->toBe($quota === null ? null : 30)
        ->and($tenant->subscriptions()->exists())->toBeTrue();
})->with([
    'Small' => [62775, 25],
    'Medium' => [62838, 50],
    'Large' => [62839, null],
]);

test('a valid historical Spark plan outside the current catalog retains access', function () {
    $tenant = Tenant::factory()
        ->withSubscription(1)
        ->create(['timezone' => 'Australia/Perth']);
    $entitlement = $tenant->entitlementSubscriptions()->with('plan')->latest('id')->firstOrFail();

    expect($entitlement->plan->getSlug())->toBe('legacy-spark-1')
        ->and($tenant->fresh()->billing_status['valid'])->toBeTrue()
        ->and($tenant->fresh()->billing_status['activeUserQuota']['quota'])->toBeNull();
});

test('a tenant covered by has_gratis receives active no-cost access', function () {
    $tenant = Tenant::factory()->create([
        'timezone' => 'Australia/Perth',
        'has_gratis' => true,
    ]);

    expect($tenant->has_gratis)->toBeTrue();

    $entitlement = $tenant->entitlementSubscriptions()->with('plan')->latest('id')->firstOrFail();

    expect($entitlement->getStatus())->toBe(SubscriptionStatus::Active)
        ->and($entitlement->plan->isFree())->toBeTrue()
        ->and($tenant->fresh()->billing_status['valid'])->toBeTrue()
        ->and(Tenant::query()->active()->whereKey($tenant->getKey())->exists())->toBeTrue();

    $initialSubscriptionCount = $tenant->entitlementSubscriptions()->count();
    $initialStartDate = $entitlement->getStartsAt();
    app(LegacyEntitlementSynchronizer::class)->sync($tenant);

    expect($tenant->entitlementSubscriptions()->count())->toBe($initialSubscriptionCount)
        ->and($tenant->entitlementSubscriptions()->latest('id')->firstOrFail()->getStartsAt()->equalTo($initialStartDate))
        ->toBeTrue();

    $tenant->customer()->firstOrFail()->update(['trial_ends_at' => null]);
    $tenant->has_gratis = false;
    $tenant->save();

    expect($tenant->fresh()->billing_status['valid'])->toBeFalse()
        ->and($tenant->fresh()->entitlementSubscriptions()->latest('id')->firstOrFail()->getStatus())
        ->toBe(SubscriptionStatus::Expired);
});

test('generic trial access and expiry use Subscriptionify entitlements', function () {
    $tenant = Tenant::factory()->create(['timezone' => 'Australia/Perth']);
    $entitlement = $tenant->entitlementSubscriptions()->latest('id')->firstOrFail();

    expect($entitlement->getStatus())->toBe(SubscriptionStatus::Trialing)
        ->and($tenant->billing_status['valid'])->toBeTrue()
        ->and(Tenant::query()->trial()->whereKey($tenant->getKey())->exists())->toBeTrue();

    $tenant->customer()->firstOrFail()->update(['trial_ends_at' => now()->subDay()]);

    expect($tenant->fresh()->billing_status['valid'])->toBeFalse()
        ->and($tenant->fresh()->billing_status['hasExpiredTrial'])->toBeTrue()
        ->and(Tenant::query()->expiredTrial()->whereKey($tenant->getKey())->exists())->toBeTrue();
});

test('last confirmed access remains valid until reconciliation expires a stale Spark entitlement', function () {
    $tenant = Tenant::query()->findOrFail('phpunit');
    $sparkSubscription = $tenant->subscriptions()->firstOrFail();

    $tenant->subscriptions()
        ->whereKey($sparkSubscription->getKey())
        ->update(['ends_at' => now()->subDay()]);

    expect($tenant->fresh()->billing_status['valid'])->toBeTrue();

    $this->artisan('billing:reconcile-legacy-entitlements')
        ->assertExitCode(0);

    expect($tenant->fresh()->billing_status['valid'])->toBeFalse()
        ->and($tenant->fresh()->entitlementSubscriptions()->latest('id')->firstOrFail()->getStatus())
        ->toBe(SubscriptionStatus::Expired);
});

test('Small tier access is retained during the quota grace period and blocked after it expires', function () {
    $tenant = Tenant::query()->findOrFail('phpunit');
    $initialActiveUserCount = $tenant->members()->active()->count();

    Membership::factory()->count(22 - $initialActiveUserCount)->create();

    expect($tenant->fresh()->billing_status['activeUserQuota'])
        ->toMatchArray([
            'quota' => 25,
            'activeUserCount' => 22,
            'quotaExceeded' => false,
            'onGracePeriod' => false,
            'quotaNearlyExceeded' => true,
        ]);

    Membership::factory()->count(4)->create();
    $overQuotaStatus = $tenant->fresh()->billing_status;

    expect($overQuotaStatus['activeUserQuota'])
        ->toMatchArray([
            'activeUserCount' => 26,
            'quotaExceeded' => false,
            'onGracePeriod' => true,
        ])
        ->and($overQuotaStatus['valid'])->toBeTrue();

    $this->travel(31)->days();
    $expiredGraceStatus = $tenant->fresh()->billing_status;

    expect($expiredGraceStatus['activeUserQuota']['quotaExceeded'])->toBeTrue()
        ->and($expiredGraceStatus['activeUserQuota']['onGracePeriod'])->toBeFalse()
        ->and($expiredGraceStatus['valid'])->toBeFalse();
});

test('an expired Subscriptionify entitlement does not grant tenant access', function () {
    $tenant = Tenant::query()->findOrFail('phpunit');
    $entitlement = $tenant->entitlementSubscriptions()->latest('id')->firstOrFail();
    $entitlement->update(['status' => SubscriptionStatus::Expired]);

    expect($tenant->fresh()->billing_status['valid'])->toBeFalse()
        ->and(Tenant::query()->active()->whereKey($tenant->getKey())->exists())->toBeFalse();
});
