<?php


namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant As BaseTenant;

/**
 * Class Tenant
 *
 * Virtual Columns
 * @property string choir_name
 * @property string timezone
 *
 * Attributes
 * @property string mail_from_name
 * @property string mail_from_address
 * @property string primary_domain
 * @property string host
 *
 * @package App\Models
 */
class Tenant extends BaseTenant
{
    use HasDomains, TenantTimezoneDates;

    public static function create(string $id, string $choir_name, string $timezone, array $attributes = []): Tenant|Model
    {
        return static::query()->create(array_merge($attributes, compact('id', 'choir_name', 'timezone')));
    }

    public static function findByDomain(string $domain): ?Tenant
    {
        return self::whereHas('domains', static function (Builder $query) use ($domain) {
            $query->where('domain', '=', $domain);
        })->first();
    }

    public function getMailFromNameAttribute(): string
    {
        return $this->choir_name.' via Choir Concierge';
    }

    public function getMailFromAddressAttribute(): string
    {
        return 'hello@'.\Request::getHost();
    }

    // @todo create a way to assign the primary domain in the database
    public function getPrimaryDomainAttribute(): string
    {
        return $this->domains->last()->domain;
    }

    public function getHostAttribute(): string
    {
        return $this->primary_domain . '.' . central_domain();
    }
}