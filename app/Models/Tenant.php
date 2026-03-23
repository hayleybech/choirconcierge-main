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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Laravel\Paddle\Billable;
use Laravel\Paddle\Subscription;
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
            if(! $tenant->logo) {
                return;
            }

            Storage::disk('public')
                ->delete('choir-logos/'.$tenant->logo);
        });
    }

    public static function create(
        string $id,
        string $name,
        string $timezone,
        array  $attributes = []
    ): self|Model {
        return static::query()->create(array_merge($attributes, compact('id', 'name', 'timezone')));
    }

    public static function findByDomain(string $domain): ?self
    {
        return self::whereHas('domains', static function (Builder $query) use ($domain) {
            $query->where('domain', '=', $domain);
        })->first();
    }

    public function getTimezoneLabelAttribute(): string
    {
        $timezone = is_string($this->timezone) ? CarbonTimeZone::create($this->timezone) : $this->timezone;

        return $timezone->toRegionName().' '.$timezone->toOffsetName();
    }

    public function getMailFromNameAttribute(): string
    {
        return $this->name.' via Choir Concierge';
    }

    public function getMailFromAddressAttribute(): string
    {
        return 'hello@'.$this->host;
    }

    public function getPrimaryDomainAttribute(): ?string
    {
        $this->load('domains');

        return $this->domains->firstWhere('is_primary')->domain
            ?? $this->domains->last()->domain
            ?? null;
    }

    public function getHostAttribute(): string
    {
        return $this->primary_domain.'.'.central_domain() ?? '';
    }

    public function logoUrl(): Attribute
    {
        return Attribute::get(fn () =>
            $this->logo ? Storage::disk()->url('choir-logos/'.$this->logo) : ''
        );
    }

    public function plan(): Attribute
    {
        return Attribute::get(function (): ?Plan {
            $planId = $this->subscription()?->paddle_plan;

            if (! $planId) {
                return null;
            }

            $plans = config('spark.billables.tenant.plans');

            foreach ($plans as $planConfig) {
                if (($planConfig['monthly_id'] ?? null) === $planId || ($planConfig['yearly_id'] ?? null) === $planId) {
                    $plan = new Plan($planConfig['name'], $planId);
                    $plan->short_description = $planConfig['short_description'] ?? null;
                    $plan->features = $planConfig['features'] ?? [];
                    $plan->options = $planConfig['options'] ?? [];

                    return $plan;
                }
            }

            return null;
        });
    }

    public function trialEndsAt(): ?Carbon
    {
        return $this->customer?->trial_ends_at;
    }

    public function hasExpiredTrial(): bool
    {
        return ! $this->onTrial() && $this->customer?->trial_ends_at?->isPast();
    }

    public function subscription(?string $name = 'default'): ?Subscription
    {
        return $this->subscriptions->firstWhere('name', $name);
    }

    public function billingStatus(): Attribute
    {
        $this->load(['subscriptions', 'customer']);
        $activeUserQuotaStatus = $this->getActiveUserQuotaStatus();

        return Attribute::get(fn () => [
            'valid' => $this->onTrial()
                || $this->has_gratis
                || ($this->subscription()?->valid() && ! $activeUserQuotaStatus['quotaExceeded']),
            'onTrial' => $this->onTrial(),
            'trialEndsAt' => $this->onTrial() ? $this->trialEndsAt() : null,
            'hasExpiredTrial' => $this->hasExpiredTrial(),
            'onGracePeriod' => $this->subscription()?->onGracePeriod() ?? false,
            'ended' => $this->subscription()?->ended() ?? false,
            'onPausedGracePeriod' => $this->subscription()?->onPausedGracePeriod() ?? false,
            'paused' => $this->subscription()?->paused() ?? false,
            'pastDue' => $this->subscription()?->pastDue() ?? false,
			'hasGratis' => $this->has_gratis,

            'activeUserQuota' => $activeUserQuotaStatus,
        ]);
    }

    public function scopeActive($query)
    {
        // valid subscription (active plan, paused grace period, or grace period), on trial or gratis
        return $query
            ->whereHas('subscriptions', function($query) {
                $query->active()
                    ->orWhere(function($query) {
                        $query->onTrial();
                    });
            })
            ->orWhereHas('customer', function($query) {
                $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', \Carbon\Carbon::now());
            })
            ->orWhere(function($query) {
                // @TODO make has_gratis a regular column
                $query->where('data->has_gratis', true);
            });
    }
    public function scopeInactive($query)
    {
        return $query
            ->whereDoesntHave('subscriptions', function($query) {
                $query->active()
                    ->orWhere(function($query) {
                        $query->onTrial();
                    });
            })
            ->whereDoesntHave('customer', function($query) {
                $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', \Carbon\Carbon::now());
            })
            ->whereNot('data->has_gratis', true);
    }
    public function scopeSubscribed($query)
    {
        return $query->whereHas('subscriptions', function($query) {
            $query->active();
        });
    }
    public function scopeEnded($query)
    {
        return $query->whereHas('subscriptions', function($query) {
            $query->ended();
        });
    }



    public function scopeTrial($query)
    {
        return $query
            ->whereHas('subscriptions', function($query) {
                $query->onTrial();
            })
            ->orWhereHas('customer', function($query) {
                $query->whereNotNull('trial_ends_at')
                    ->where('trial_ends_at', '>', \Carbon\Carbon::now());
            });
    }
    public function scopeGratis($query)
    {
        // @TODO make has_gratis a regular column
        return $query->where('data->has_gratis', true);
    }


	public function setupDone(): Attribute
	{
		return Attribute::get(fn() => Membership::query()
			->where('tenant_id', $this->id)
			->where('user_id', $this->created_by)
			->exists());
	}

    public function getActiveUserQuotaStatus(): array
    {
        // Load config
        $quota = $this->plan ? $this->plan->options['activeUserQuota'] : null;
        $quotaBuffer = $this->plan ? $this->plan->options['activeUserQuotaBuffer'] : null;
        $gracePeriodDays = $this->plan ? $this->plan->options['activeUserGracePeriodDays'] : null;

        $activeUserCount = $this->members()
            ->active()
            ->count();
        $lastUserCreatedAt = $this->members()
            ->orderBy('created_at', 'desc')
            ->value('created_at');
        $gracePeriodEndsAt = $lastUserCreatedAt ? Carbon::make($lastUserCreatedAt)->addDays($gracePeriodDays ?? 0) : null;

        return [
            'quota' => $quota,
            'activeUserCount' => $activeUserCount,
            'quotaExceeded' => $quota
                && $gracePeriodDays !== null
                && ($quota !== null && $activeUserCount > $quota)
                && $gracePeriodEndsAt?->isFuture(),
            'onGracePeriod' => $quota
                && $gracePeriodDays !== null
                && ($quota !== null && $activeUserCount > $quota)
                && $gracePeriodEndsAt?->isFuture(),
            'gracePeriodEndsAt' => $gracePeriodEndsAt,
            'quotaNearlyExceeded' => $quota
                && $quotaBuffer !== null
                && $activeUserCount < $quota
                && ($quota !== null && ($activeUserCount + $quotaBuffer) > $quota),
        ];
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

	public function ensembles(): HasMany {
		return $this->hasMany(Ensemble::class);
	}

    public function members(): HasMany {
        return $this->hasMany(Membership::class);
    }

    public function customFields(): HasMany {
        return $this->hasMany(CustomField::class);
    }

    public function billingUser(): BelongsTo {
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
