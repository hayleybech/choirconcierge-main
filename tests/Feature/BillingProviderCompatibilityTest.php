<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

test('classic and billing providers register separate webhook routes', function () {
    $routes = Route::getRoutes();

    expect($routes->getByName('cashier.classic.webhook')?->uri())->toBe('paddle/webhook');
    expect($routes->getByName('cashier.webhook')?->uri())->toBe('paddle-billing/webhook');
    expect($routes->getByName('spark.portal')?->uri())->toBe('app/billing/{type?}/{id?}');
    expect($routes->getByName('cashier.classic.webhook')?->getActionName())
        ->toContain('ChoirConcierge\LegacyCashierPaddle\Http\Controllers\WebhookController');
    expect($routes->getByName('cashier.webhook')?->getActionName())
        ->toContain('Laravel\Paddle\Http\Controllers\WebhookController');
    expect($routes->getByName('spark.portal')?->getActionName())
        ->toContain('ChoirConcierge\LegacySpark\Http\Controllers\BillingPortalController');
});

test('classic, billing, and plan subscriptions use separate tables', function () {
    expect((new ChoirConcierge\LegacyCashierPaddle\Customer())->getTable())->toBe('classic_customers');
    expect((new ChoirConcierge\LegacyCashierPaddle\Subscription())->getTable())->toBe('classic_subscriptions');
    expect((new ChoirConcierge\LegacyCashierPaddle\Receipt())->getTable())->toBe('receipts');

    expect((new App\Models\Billing\Customer())->getTable())->toBe('customers');
    expect((new App\Models\Billing\Subscription())->getTable())->toBe('subscriptions');
    expect((new App\Models\Billing\SubscriptionItem())->getTable())->toBe('subscription_items');
    expect((new App\Models\Billing\Transaction())->getTable())->toBe('transactions');
    expect((new Revoltify\Subscriptionify\Models\Subscription())->getTable())->toBe('plan_subscriptions');
    expect((new Revoltify\Subscriptionify\Models\Feature())->getTable())->toBe('entitlements');
    expect(Schema::hasTable('subscriptions'))->toBeTrue();
    expect(Schema::hasTable('classic_subscriptions'))->toBeTrue();
    expect(Schema::hasTable('plan_subscriptions'))->toBeTrue();
    expect(Schema::hasTable('entitlements'))->toBeTrue();
    expect(config('subscriptionify.tables'))->toBe([
        'plans' => 'plans',
        'features' => 'entitlements',
        'feature_plan' => 'feature_plan',
        'subscriptions' => 'plan_subscriptions',
        'feature_usages' => 'feature_usages',
        'feature_subscribable' => 'feature_subscribable',
    ]);
});
