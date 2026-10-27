<?php

namespace App\Observers;

use App\Models\Tenant;
use App\Services\Billing\LegacyEntitlementSynchronizer;
use ChoirConcierge\LegacyCashierPaddle\Customer;

class LegacyCashierCustomerObserver
{
    public function saved(Customer $customer): void
    {
        $this->synchronize($customer);
    }

    public function deleted(Customer $customer): void
    {
        $this->synchronize($customer);
    }

    private function synchronize(Customer $customer): void
    {
        $customer->loadMissing('billable');
        $tenant = $customer->billable;

        if ($tenant instanceof Tenant) {
            app(LegacyEntitlementSynchronizer::class)->sync($tenant);
        }
    }
}
