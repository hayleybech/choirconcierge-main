<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Services\Billing\LegacyEntitlementSynchronizer;
use ChoirConcierge\LegacyCashierPaddle\Subscription;

class LegacyCashierSubscriptionObserver
{
    public function saved(Subscription $subscription): void
    {
        $this->synchronize($subscription);
    }

    public function deleted(Subscription $subscription): void
    {
        $this->synchronize($subscription);
    }

    private function synchronize(Subscription $subscription): void
    {
        $subscription->loadMissing('billable');
        $tenant = $subscription->billable;

        if ($tenant instanceof Tenant) {
            app(LegacyEntitlementSynchronizer::class)->sync($tenant);
        }
    }
}
