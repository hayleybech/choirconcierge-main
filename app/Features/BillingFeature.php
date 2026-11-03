<?php

namespace App\Features;

use App\Models\Tenant;
use Laravel\Pennant\Feature;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;

class BillingFeature
{
    public const string TENANT_FEATURE = 'billing-v2';

    public static function define(): void
    {
        Feature::define(self::TENANT_FEATURE, fn (TenantContract $tenant): bool => false);
    }

    public static function tenantEnabled(TenantContract $tenant): bool
    {
        return Feature::for($tenant)->active(self::TENANT_FEATURE);
    }

    public static function setTenantEnabled(Tenant $tenant, bool $enabled): void
    {
        Feature::for($tenant)->activate(self::TENANT_FEATURE, $enabled);
    }
}
