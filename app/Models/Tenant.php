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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spark\Billable;
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
 * @property string $created_by ID of user that created the Tenant
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

            Storage::disk('global-public')
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
            $this->logo ? asset('storage/choir-logos/'.$this->logo) : ''
        );
    }

    public function plan(): Attribute
    {
        return Attribute::get(fn() => $this->sparkPlan());
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
        $quota = $this->sparkPlan() ? $this->sparkPlan()->options['activeUserQuota'] : null;
        $quotaBuffer = $this->sparkPlan() ? $this->sparkPlan()->options['activeUserQuotaBuffer'] : null;
        $gracePeriodDays = $this->sparkPlan() ? $this->sparkPlan()->options['activeUserGracePeriodDays'] : null;

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
                && $activeUserCount > $quota
                && $gracePeriodEndsAt?->isFuture(),
            'onGracePeriod' => $quota
                && $gracePeriodDays !== null
                && $activeUserCount > $quota
                && $gracePeriodEndsAt?->isFuture(),
            'gracePeriodEndsAt' => $gracePeriodEndsAt,
            'quotaNearlyExceeded' => $quota
                && $quotaBuffer !== null
                && $activeUserCount < $quota
                && ($activeUserCount + $quotaBuffer) > $quota,
        ];
    }

	/**
	 * @throws Exception
	 */
	public function updateLogo(UploadedFile|string $logo, string $hash_name)
    {
        if (!Storage::disk('global-public')->exists('choir-logos')) {
            Storage::disk('global-public')->makeDirectory('choir-logos');
        }
        if (!Storage::disk('global-public')
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

    public function billingUser(): BelongsTo {
        return $this->belongsTo(User::class, 'billing_user_id');
    }

    public function paddleEmail()
    {
        return $this->billingUser?->email;
    }
}
