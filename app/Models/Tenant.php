<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Carbon\CarbonTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;

/**
 * Class Tenant
 *
 * Virtual Columns
 * @property string choir_name
 *
 * Attributes
 * @property CarbonTimeZone timezone from virtual column 'timezone'
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

	protected $appends = ['host', 'timezone_label'];

	public static function create(
		string $id,
		string $choir_name,
		string $timezone,
		array $attributes = []
	): Tenant|Model {
		return static::query()->create(array_merge($attributes, compact('id', 'choir_name', 'timezone')));
	}

	public static function findByDomain(string $domain): ?Tenant
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
		return $this->choir_name . ' via Choir Concierge';
	}

	public function getMailFromAddressAttribute(): string
	{
		return 'hello@' . $this->host;
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
