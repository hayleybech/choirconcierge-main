<?php

use App\Http\Middleware\HandleInertiaRequests as AppInertiaMiddleware;
use ChoirConcierge\LegacySpark\Http\Middleware\HandleInertiaRequests as SparkInertiaMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as RequestFacade;
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

test('inertia visits crossing the app and spark boundary trigger full document navigation', function () {
    config(['spark.path' => 'app/billing']);

    $sparkRequest = Request::create('/app/billing', 'GET', server: ['HTTP_X_INERTIA' => 'true']);
    RequestFacade::swap($sparkRequest);

    $sparkResponse = app(SparkInertiaMiddleware::class)->handle($sparkRequest, fn () => response('Should not run'));

    expect($sparkResponse->getStatusCode())->toBe(409)
        ->and($sparkResponse->headers->get('X-Inertia-Location'))->toBe($sparkRequest->fullUrl());

    $appRequest = Request::create('/app/dashboard', 'GET', server: [
        'HTTP_X_INERTIA' => 'true',
        'HTTP_REFERER' => 'https://example.test/app/billing',
    ]);
    RequestFacade::swap($appRequest);

    $appResponse = app(AppInertiaMiddleware::class)->handle($appRequest, fn () => response('Should not run'));

    expect($appResponse->getStatusCode())->toBe(409)
        ->and($appResponse->headers->get('X-Inertia-Location'))->toBe($appRequest->fullUrl());
});
