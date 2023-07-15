<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Carbon\CarbonTimeZone;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Class Tenant
 *
 * Virtual Columns
 * @property string $name
 * @property string $logo
 * @property Carbon $renews_at
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
 */
class Tenant extends BaseTenant
{
    use HasDomains, TenantTimezoneDates;

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

    public function getTimezoneAttribute($value): CarbonTimeZone
    {
        return new CarbonTimeZone($value);
    }

    public function getTimezoneLabelAttribute(): string
    {
        return $this->timezone->toRegionName().' '.$this->timezone->toOffsetName();
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
}
