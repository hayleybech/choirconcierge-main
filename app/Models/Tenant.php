<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Carbon\CarbonTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Class Tenant
 *
 * Virtual Columns
 * @property string $choir_name
 * @property string $choir_logo
 *
 * Attributes
 * @property CarbonTimeZone timezone from virtual column 'timezone'
 * @property string mail_from_name
 * @property string mail_from_address
 * @property string primary_domain
 * @property string host
 * @property string $logo_url
 */
class Tenant extends BaseTenant
{
    use HasDomains, TenantTimezoneDates;

    protected $appends = ['host', 'timezone_label', 'logo_url'];

    public static function create(
        string $id,
        string $choir_name,
        string $timezone,
        array $attributes = []
    ): self|Model {
        return static::query()->create(array_merge($attributes, compact('id', 'choir_name', 'timezone')));
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
        return $this->choir_name.' via Choir Concierge';
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
            asset('storage/choir-logos/'.$this->choir_logo
        ));
    }
}
