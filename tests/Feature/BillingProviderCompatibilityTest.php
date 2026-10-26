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

test('classic, billing, and entitlement models use separate subscription tables', function () {
    expect((new ChoirConcierge\LegacyCashierPaddle\Customer())->getTable())->toBe('customers');
    expect((new ChoirConcierge\LegacyCashierPaddle\Subscription())->getTable())->toBe('subscriptions');
    expect((new ChoirConcierge\LegacyCashierPaddle\Receipt())->getTable())->toBe('receipts');

    expect((new App\Models\Billing\Customer())->getTable())->toBe('billing_customers');
    expect((new App\Models\Billing\Subscription())->getTable())->toBe('billing_subscriptions');
    expect((new App\Models\Billing\SubscriptionItem())->getTable())->toBe('billing_subscription_items');
    expect((new App\Models\Billing\Transaction())->getTable())->toBe('billing_transactions');
    expect((new Revoltify\Subscriptionify\Models\Subscription())->getTable())->toBe('ent_subscriptions');
    expect((new Revoltify\Subscriptionify\Models\Feature())->getTable())->toBe('ent_features');
    expect(Schema::hasTable('subscriptions'))->toBeTrue();
    expect(Schema::hasTable('billing_subscriptions'))->toBeTrue();
    expect(Schema::hasTable('ent_subscriptions'))->toBeTrue();
    expect(Schema::hasTable('features'))->toBeTrue();
    expect(Schema::hasTable('ent_features'))->toBeTrue();
    expect(config('subscriptionify.tables'))->toBe([
        'plans' => 'ent_plans',
        'features' => 'ent_features',
        'feature_plan' => 'ent_plan_features',
        'subscriptions' => 'ent_subscriptions',
        'feature_usages' => 'ent_feature_usage',
        'feature_subscribable' => 'ent_feature_grants',
    ]);
});
