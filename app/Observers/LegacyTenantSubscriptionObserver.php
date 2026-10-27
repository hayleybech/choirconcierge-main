<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Services\Billing\LegacySubscriptionSynchronizer;

class LegacyTenantSubscriptionObserver
{
    public function saved(Tenant $tenant): void
    {
        if (!$tenant->wasRecentlyCreated && !$tenant->wasChanged('data')) {
            return;
        }

        $subscription = $tenant->planSubscriptions()->latest('id')->first();
        $hasGratisSubscription = $subscription?->getPlan()->isFree() ?? false;

        if ($tenant->has_gratis !== $hasGratisSubscription) {
            app(LegacySubscriptionSynchronizer::class)->sync($tenant);
        }
    }
}
