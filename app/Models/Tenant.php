<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant As BaseTenant;

class Tenant extends BaseTenant
{
    use HasDomains;

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

}