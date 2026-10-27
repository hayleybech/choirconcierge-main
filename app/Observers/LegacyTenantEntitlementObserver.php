<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Services\Billing\LegacyEntitlementSynchronizer;

class LegacyTenantEntitlementObserver
{
    public function saved(Tenant $tenant): void
    {
        if (!$tenant->wasRecentlyCreated && !$tenant->wasChanged('data')) {
            return;
        }

        $entitlement = $tenant->entitlementSubscriptions()->latest('id')->first();
        $hasGratisEntitlement = $entitlement?->getPlan()->isFree() ?? false;

        if ($tenant->has_gratis !== $hasGratisEntitlement) {
            app(LegacyEntitlementSynchronizer::class)->sync($tenant);
        }
    }
}
