<?php

namespace ChoirConcierge\LegacySpark\Actions;

use ChoirConcierge\LegacySpark\Contracts\Actions\GeneratesSubscriptionPayLinks;
use ChoirConcierge\LegacySpark\Spark;

class GenerateSubscriptionPayLink implements GeneratesSubscriptionPayLinks
{
    /**
     * {@inheritDoc}
     */
    public function generatePayLink($billable, $plan, array $options = [])
    {
        $builder = $billable->newSubscription('default', $plan);

        // Paddle wouldn't allow changing plans while on trial so we disable any trials
        // in Spark as customers won't be able to change plans or even change plan's
        // quantity when they are still within a Paddle subscription's trial time.
        $builder->skipTrial();

        $type = $billable->sparkConfiguration('type');

        if (Spark::chargesPerSeat($type)) {
            $builder->quantity(Spark::seatCount($type, $billable));
        }

        return $builder->create();
    }
}
