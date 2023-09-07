<?php

use App\Models\Tenant;

return [

    /*
    |--------------------------------------------------------------------------
    | Spark Path
    |--------------------------------------------------------------------------
    |
    | This configuration option determines the URI at which the Spark billing
    | portal is available. You are free to change this URI to a value that
    | you prefer. You shall link to this location from your application.
    |
    */

    'path' => '/app/billing',

    'dashboard_url' => '/app',

    'terms_url' => 'https://www.choirconcierge.com/terms-of-service/',

    /*
    |--------------------------------------------------------------------------
    | Spark Middleware
    |--------------------------------------------------------------------------
    |
    | These are the middleware that requests to the Spark billing portal must
    | pass through before being accepted. Typically, the default list that
    | is defined below should be suitable for most Laravel applications.
    |
    */

    'middleware' => ['web', 'auth'],

    /*
    |--------------------------------------------------------------------------
    | Branding
    |--------------------------------------------------------------------------
    |
    | These configuration values allow you to customize the branding of the
    | billing portal, including the primary color and the logo that will
    | be displayed within the billing portal. This logo value must be
    | the absolute path to an SVG logo within the local filesystem.
    |
    */

     'brand' =>  [
         'logo' => realpath(__DIR__.'/../public/img/vibrant/logo-dark.svg'),
         'color' => 'bg-purple-800',
     ],

    /*
    |--------------------------------------------------------------------------
    | Proration Behavior
    |--------------------------------------------------------------------------
    |
    | This value determines if charges are prorated when making adjustments
    | to a plan such as incrementing or decrementing the quantity of the
    | plan. This also determines proration behavior if changing plans.
    |
    */

    'prorates' => true,

    /*
    |--------------------------------------------------------------------------
    | Spark Date Format
    |--------------------------------------------------------------------------
    |
    | This date format will be utilized by Spark to format dates in various
    | locations within the billing portal, such as while showing invoice
    | dates. You should customize the format based on your own locale.
    |
    */

    'date_format' => 'F j, Y',

    /*
    |--------------------------------------------------------------------------
    | Spark Billables
    |--------------------------------------------------------------------------
    |
    | Below you may define billable entities supported by your Spark driven
    | application. You are free to have multiple billable entities which
    | can each define multiple subscription plans available for users.
    |
    | In addition to defining your billable entity, you may also define its
    | plans and the plan's features, including a short description of it
    | as well as a "bullet point" listing of its distinctive features.
    |
    */

    'billables' => [

        'tenant' => [
            'model' => Tenant::class,

            'trial_days' => 30,

            'default_interval' => 'yearly',

            'plans' => [
                [
                    'name' => 'Small Choir',
                    'short_description' => 'Up to 25 users.',
                    'yearly_id' => env('SPARK_PLAN_SMALL_YEARLY', 62775),
                    'features' => [
                        'Up to 25 users',
                        'Unmetered storage',
                        '20% off first year with coupon code FIRSTYR',
                    ],
                    'archived' => false,
                    'options' => [
                        'activeUserQuota' => 25,
                        'activeUserQuotaBuffer' => 5,
                        'activeUserGracePeriodDays' => 30,
                    ]
                ],
                [
                    'name' => 'Medium Choir',
                    'short_description' => 'Up to 50 users.',
                    'yearly_id' => env('SPARK_PLAN_MEDIUM_YEARLY', 62838),
                    'features' => [
                        'Up to 50 users',
                        'Unmetered storage',
                        '20% off first year with coupon code FIRSTYR',
                    ],
                    'archived' => false,
                    'options' => [
                        'activeUserQuota' => 50,
                        'activeUserQuotaBuffer' => 5,
                        'activeUserGracePeriodDays' => 30,
                    ]
                ],
                [
                    'name' => 'Large Choir',
                    'short_description' => '51+ users.',
                    'yearly_id' => env('SPARK_PLAN_LARGE_YEARLY', 62839),
                    'features' => [
                        'Unlimited users',
                        'Unmetered storage',
                        '20% off first year with coupon code FIRSTYR',
                    ],
                    'archived' => false,
                    'options' => [
                        'activeUserQuota' => null,
                        'activeUserQuotaBuffer' => null,
                        'activeUserGracePeriodDays' => null,
                    ]
                ],
            ],

        ],

    ],
];
