<?php

namespace App\Models\Billing;

use Laravel\Paddle\Transaction as CashierTransaction;

class Transaction extends CashierTransaction
{
    protected $table = 'billing_transactions';
}
