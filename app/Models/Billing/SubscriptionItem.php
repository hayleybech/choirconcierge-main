<?php

namespace App\Models\Billing;

use Laravel\Paddle\SubscriptionItem as CashierSubscriptionItem;

class SubscriptionItem extends CashierSubscriptionItem
{
    protected $table = 'billing_subscription_items';
}
