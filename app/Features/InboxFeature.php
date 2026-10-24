<?php

namespace App\Features;

use App\Models\Tenant;
use App\Models\User;
use Laravel\Pennant\Feature;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;

class InboxFeature
{
    public const string TENANT_FEATURE = 'inbox';

    public const string SUPER_ADMIN_OVERRIDE_FEATURE = 'inbox-super-admin-override';

    public static function define(): void
    {
        Feature::define(self::TENANT_FEATURE, fn (Tenant $tenant): bool => false);
        Feature::define(self::SUPER_ADMIN_OVERRIDE_FEATURE, fn (User $user): ?bool => null);
    }

    public static function isEnabledFor(TenantContract $tenant, ?User $user = null): bool
    {
        $override = $user?->isSuperAdmin ? self::personalOverride($user) : null;

        return $override ?? Feature::for($tenant)->active(self::TENANT_FEATURE);
    }

    public static function tenantEnabled(Tenant $tenant): bool
    {
        return Feature::for($tenant)->active(self::TENANT_FEATURE);
    }

    public static function personalOverride(User $user): ?bool
    {
        $value = Feature::for($user)->value(self::SUPER_ADMIN_OVERRIDE_FEATURE);

        return is_bool($value) ? $value : null;
    }

    public static function setTenantEnabled(Tenant $tenant, bool $enabled): void
    {
        Feature::for($tenant)->activate(self::TENANT_FEATURE, $enabled);
    }

    public static function setPersonalOverride(User $user, ?bool $enabled): void
    {
        if ($enabled === null) {
            Feature::for($user)->forget(self::SUPER_ADMIN_OVERRIDE_FEATURE);

            return;
        }

        Feature::for($user)->activate(self::SUPER_ADMIN_OVERRIDE_FEATURE, $enabled);
    }
}
