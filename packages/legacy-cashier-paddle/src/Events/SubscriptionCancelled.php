<?php

namespace ChoirConcierge\LegacyCashierPaddle\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use ChoirConcierge\LegacyCashierPaddle\Subscription;

class SubscriptionCancelled
{
    use Dispatchable, SerializesModels;

    /**
     * The subscription instance.
     *
     * @var \ChoirConcierge\LegacyCashierPaddle\Subscription
     */
    public $subscription;

    /**
     * The webhook payload.
     *
     * @var array
     */
    public $payload;

    /**
     * Create a new event instance.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @param  array  $payload
     * @return void
     */
    public function __construct(Subscription $subscription, array $payload)
    {
        $this->subscription = $subscription;
        $this->payload = $payload;
    }
}
