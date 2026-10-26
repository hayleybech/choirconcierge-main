<?php

declare(strict_types=1);

use Revoltify\Subscriptionify\Models\Feature;
use Revoltify\Subscriptionify\Models\Plan;
use Revoltify\Subscriptionify\Models\Subscription;

return [

    /*
    |--------------------------------------------------------------------------
    | Models
    |--------------------------------------------------------------------------
    |
    | Customise the Eloquent models used by the package. Your custom models
    | should extend the package's base models or implement the corresponding
    | contract interfaces (HasPlan, HasFeature, HasSubscription).
    |
    */

    'models' => [
        'plan' => Plan::class,
        'feature' => Feature::class,
        'subscription' => Subscription::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Table Names
    |--------------------------------------------------------------------------
    |
    | If your application already uses one of the default table names
    | (e.g. you have a "subscriptions" table from Cashier), you can
    | rename any Subscriptionify table here.
    |
    */

    'tables' => [
        'plans' => 'ent_plans',
        'features' => 'ent_features',
        'feature_plan' => 'ent_plan_features',
        'subscriptions' => 'ent_subscriptions',
        'feature_usages' => 'ent_feature_usage',
        'feature_subscribable' => 'ent_feature_grants',
    ],

    /*
    |--------------------------------------------------------------------------
    | Middleware Aliases
    |--------------------------------------------------------------------------
    |
    | The route middleware aliases registered by the package.
    | Rename them here if they conflict with your application's existing aliases.
    |
    */

    'middleware' => [
        'subscribed' => 'subscribed',
        'plan' => 'plan',
        'feature' => 'feature',
    ],

];
