<?php

namespace ChoirConcierge\LegacyCashierPaddle;

use ChoirConcierge\LegacyCashierPaddle\Concerns\ManagesCustomer;
use ChoirConcierge\LegacyCashierPaddle\Concerns\ManagesReceipts;
use ChoirConcierge\LegacyCashierPaddle\Concerns\ManagesSubscriptions;
use ChoirConcierge\LegacyCashierPaddle\Concerns\PerformsCharges;

trait Billable
{
    use ManagesCustomer;
    use ManagesSubscriptions;
    use ManagesReceipts;
    use PerformsCharges;

    /**
     * Get the default Paddle API options for the current Billable model.
     *
     * @param  array  $options
     * @return array
     */
    public function paddleOptions(array $options = [])
    {
        return Cashier::paddleOptions($options);
    }
}
