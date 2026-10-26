<?php

namespace ChoirConcierge\LegacySpark\Contracts\Actions;

interface GeneratesSubscriptionPayLinks
{
    /**
     * Generate a pay link for a new subscription.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Billable  $billable
     * @param  string  $plan
     * @return mixed
     */
    public function generatePayLink($billable, $plan, array $options = []);
}
