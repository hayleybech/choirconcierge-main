<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Carbon\CarbonTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use ChoirConcierge\LegacySpark\Billable;
use Revoltify\Subscriptionify\Enums\SubscriptionStatus;
use Revoltify\Subscriptionify\Models\Subscription as PlanSubscription;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Class Tenant
 *
 * Virtual Columns
 * @property string $name
 * @property string $logo
 * @property Carbon $renews_at
 * @property bool $has_gratis
 * @property bool $had_demo
 * @property string $fee_freq Memorise renew fee dialog setting
 * @property string $created_by ID of user that created the Tenant
 * @property array $widgets_upcoming_events_categories Setting: IDs of event types chosen to show in the widget
 *
 * Attributes
 * @property CarbonTimeZone timezone from virtual column 'timezone'
 * @property string mail_from_name
 * @property string mail_from_address
 * @property string primary_domain
 * @property string host
 * @property string $logo_url
 *
 * Relationships
 * @property Collection<Ensemble> $ensembles
 * @property Collection<Membership> $members
 * @property Collection<CustomField> $custom_fields
 * @protected User $billingUser
 */
class Tenant extends BaseTenant
{
    use HasDomains, TenantTimezoneDates, Billable, HasFactory;

    protected $appends = ['host', 'timezone_label', 'logo_url'];

    protected static function booted(): void
    {
        static::deleted(function (Tenant $tenant) {
            if (!$tenant->logo) {
                return;
            }

            Storage::disk('public')
                ->delete('choir-logos/' . $tenant->logo);
        });
    }

    public static function create(
        string $id,
        string $name,
        string $timezone,
        array  $attributes = []
    ): self|Model
    {
        return static::query()->create(array_merge($attributes, compact('id', 'name', 'timezone')));
    }

    public static function findByDomain(string $domain): ?self
    {
        return self::with('domains')->whereHas('domains', static function (Builder $query) use ($domain) {
            $query->where('domain', '=', $domain);
        })->first();
    }

    public function getTimezoneLabelAttribute(): string
    {
        $timezone = is_string($this->timezone) ? CarbonTimeZone::create($this->timezone) : $this->timezone;

        return $timezone->toRegionName() . ' ' . $timezone->toOffsetName();
    }

    public function getMailFromNameAttribute(): string
    {
        return $this->name . ' via Choir Concierge';
    }

    public function getMailFromAddressAttribute(): string
    {
        return 'hello@' . $this->host;
    }

    public function getPrimaryDomainAttribute(): ?string
    {
        $this->loadMissing('domains');

        return $this->domains->firstWhere('is_primary')->domain
            ?? $this->domains->last()->domain
            ?? null;
    }

    public function getHostAttribute(): string
    {
        return $this->primary_domain . '.' . central_domain() ?? '';
    }

    public function logoUrl(): Attribute
    {
        return Attribute::get(fn() => $this->logo ? Storage::disk()->url('choir-logos/' . $this->logo) : ''
        );
    }

    public function plan(): Attribute
    {
        return Attribute::get(fn() => $this->sparkPlan());
    }

    public function billingStatus(): Attribute
    {
        $subscription = $this->currentSubscription();
        $activeUserQuotaStatus = $this->getActiveUserQuotaStatus($subscription);
        $onTrial = $subscription?->onTrial() ?? false;
        $hasGratis = $subscription?->getPlan()->isFree() ?? false;

        return Attribute::get(fn (): array => [
            'valid' => $this->hasValidSubscription($subscription) && !$activeUserQuotaStatus['quotaExceeded'],
            'onTrial' => $onTrial,
            'trialEndsAt' => $onTrial ? $subscription?->getTrialEndsAt() : null,
            'hasExpiredTrial' => in_array($subscription?->getStatus(), [
                SubscriptionStatus::Trialing,
                SubscriptionStatus::Expired,
            ], true)
                && $subscription->getTrialEndsAt()?->isPast() === true,
            'onGracePeriod' => $subscription?->onGracePeriod() ?? false,
            'ended' => $subscription?->ended() ?? false,
            'onPausedGracePeriod' => false,
            'paused' => false,
            'pastDue' => $subscription?->pastDue() ?? false,
            'hasGratis' => $hasGratis,

            'activeUserQuota' => $activeUserQuotaStatus,
        ]);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->whereHas('planSubscriptions', function (Builder $query): void {
            $this->applyValidSubscriptionFilter($query);
        });
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->whereDoesntHave('planSubscriptions', function (Builder $query): void {
            $this->applyValidSubscriptionFilter($query);
        });
    }

    public function scopeSubscribed(Builder $query): Builder
    {
        return $query->whereHas('planSubscriptions', function (Builder $query): void {
            $query->where(function (Builder $query): void {
                $query->where(function (Builder $query): void {
                    $query->whereIn('status', [
                        SubscriptionStatus::Active->value,
                        SubscriptionStatus::PastDue->value,
                    ])->where(function (Builder $query): void {
                        $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
                    });
                })->orWhere(function (Builder $query): void {
                    $query->where('status', SubscriptionStatus::Cancelled->value)
                        ->where('ends_at', '>', now());
                });
            });
        });
    }

    public function scopeEnded(Builder $query): Builder
    {
        return $query->whereHas('planSubscriptions', function (Builder $query): void {
            $query->where('status', SubscriptionStatus::Expired->value)
                ->orWhere(function (Builder $query): void {
                    $query->where('status', SubscriptionStatus::Cancelled->value)
                        ->whereNotNull('ends_at')
                        ->where('ends_at', '<=', now());
                });
        });
    }

    public function scopeTrial(Builder $query): Builder
    {
        return $query->whereHas('planSubscriptions', function (Builder $query): void {
            $query->where('status', SubscriptionStatus::Trialing->value)
                ->where('trial_ends_at', '>', now());
        });
    }

    public function scopeExpiredTrial(Builder $query): Builder
    {
        return $query->whereHas('planSubscriptions', function (Builder $query): void {
            $query->whereIn('status', [
                SubscriptionStatus::Trialing->value,
                SubscriptionStatus::Expired->value,
            ])->whereNotNull('trial_ends_at')
                ->where('trial_ends_at', '<=', now());
        });
    }

    public function scopeGratis(Builder $query): Builder
    {
        return $query->where('data->has_gratis', true);
    }


    public function setupDone(): Attribute
    {
        return Attribute::get(fn() => Membership::query()
            ->where('tenant_id', $this->id)
            ->where('user_id', $this->created_by)
            ->exists());
    }

    /**
     * @return array{
     *     quota: int|null,
     *     activeUserCount: int,
     *     quotaExceeded: bool,
     *     onGracePeriod: bool,
     *     gracePeriodEndsAt: Carbon|null,
     *     quotaNearlyExceeded: bool
     * }
     */
    public function getActiveUserQuotaStatus(?PlanSubscription $subscription = null): array
    {
        $subscription ??= $this->currentSubscription();
        $features = $subscription?->getPlan()->features ?? collect();
        $quota = $this->featureLimit($features, 'active-user-quota');
        $quotaBuffer = $this->featureLimit($features, 'active-user-quota-buffer');
        $gracePeriodDays = $this->featureLimit($features, 'active-user-grace-period-days');

        $activeUserCount = $this->members()
            ->active()
            ->count();
        $lastUserCreatedAt = $this->members()
            ->orderBy('created_at', 'desc')
            ->value('created_at');
        $gracePeriodEndsAt = $lastUserCreatedAt ? Carbon::make($lastUserCreatedAt)->addDays($gracePeriodDays ?? 0) : null;
        $isOverQuota = $quota !== null && $activeUserCount > $quota;
        $onGracePeriod = $isOverQuota && $gracePeriodDays !== null && $gracePeriodEndsAt?->isFuture() === true;

        return [
            'quota' => $quota,
            'activeUserCount' => $activeUserCount,
            'quotaExceeded' => $isOverQuota && !$onGracePeriod,
            'onGracePeriod' => $onGracePeriod,
            'gracePeriodEndsAt' => $gracePeriodEndsAt,
            'quotaNearlyExceeded' => $quota !== null
                && $quotaBuffer !== null
                && $activeUserCount < $quota
                && ($activeUserCount + $quotaBuffer) > $quota,
        ];
    }

    public function planSubscriptions(): MorphMany
    {
        $subscriptionModel = config('subscriptionify.models.subscription', PlanSubscription::class);

        return $this->morphMany($subscriptionModel, 'subscribable');
    }

    private function currentSubscription(): ?PlanSubscription
    {
        return $this->planSubscriptions()
            ->with('plan.features')
            ->latest('id')
            ->first();
    }

    private function hasValidSubscription(?PlanSubscription $subscription): bool
    {
        if ($subscription === null) {
            return false;
        }

        return match ($subscription->getStatus()) {
            SubscriptionStatus::Active, SubscriptionStatus::PastDue => $subscription->getEndsAt()?->isFuture() ?? true,
            SubscriptionStatus::Trialing => $subscription->onTrial(),
            SubscriptionStatus::Cancelled => $subscription->onGracePeriod(),
            SubscriptionStatus::Expired => false,
        };
    }

    private function applyValidSubscriptionFilter(Builder $query): void
    {
        $query->where(function (Builder $query): void {
            $query->where(function (Builder $query): void {
                $query->whereIn('status', [
                    SubscriptionStatus::Active->value,
                    SubscriptionStatus::PastDue->value,
                ])->where(function (Builder $query): void {
                    $query->whereNull('ends_at')->orWhere('ends_at', '>', now());
                });
            })->orWhere(function (Builder $query): void {
                $query->where('status', SubscriptionStatus::Trialing->value)
                    ->where('trial_ends_at', '>', now());
            })->orWhere(function (Builder $query): void {
                $query->where('status', SubscriptionStatus::Cancelled->value)
                    ->where('ends_at', '>', now());
            });
        });
    }

    /**
     * @param Collection<int, \Revoltify\Subscriptionify\Models\Feature> $features
     */
    private function featureLimit(Collection $features, string $slug): ?int
    {
        $value = $features->firstWhere('slug', $slug)?->pivot?->value;

        return $value === null ? null : (int) $value;
    }

    /**
     * @throws Exception
     */
    public function updateLogo(UploadedFile|string $logo, string $hash_name)
    {
        if (!Storage::disk()->exists('choir-logos')) {
            Storage::disk()->makeDirectory('choir-logos');
        }
        if (!Storage::disk()
            ->putFileAs('choir-logos', $logo, $hash_name)
        ) {
            throw new Exception('Failed to save the logo.');
        }

        $this->update(['logo' => $hash_name]);
    }

    public function ensembles(): HasMany
    {
        return $this->hasMany(Ensemble::class);
    }

    public function members(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    public function customFields(): HasMany
    {
        return $this->hasMany(CustomField::class);
    }

    public function billingUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'billing_user_id');
    }

    public function paddleEmail()
    {
        return $this->billingUser?->email;
    }

    public function mailLogs(): BelongsToMany
    {
        return $this->belongsToMany(MailLog::class, 'mail_log_tenant');
    }
}
