<?php

use App\Models\Tenant;

return [

    /*
    |--------------------------------------------------------------------------
    | Paddle Keys
    |--------------------------------------------------------------------------
    |
    | The Paddle vendor ID and auth code will allow your application to call
    | the Paddle API. The "public" key is typically used when interacting
    | with Paddle.js while the "secret" key accesses private endpoints.
    |
    */

    'vendor_id' => env('PADDLE_VENDOR_ID'),

    'vendor_auth_code' => env('PADDLE_VENDOR_AUTH_CODE'),

    'public_key' => env('PADDLE_PUBLIC_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Cashier Path
    |--------------------------------------------------------------------------
    |
    | This is the base URI path where Cashier's views, such as the webhook
    | route, will be available. You're free to tweak this path based on
    | the needs of your particular application or design preferences.
    |
    */

    'path' => env('CASHIER_PATH', 'paddle'),

    /*
    |--------------------------------------------------------------------------
    | Cashier Webhook
    |--------------------------------------------------------------------------
    |
    | This is the base URI where webhooks from Paddle will be sent. The URL
    | built into Cashier Paddle is used by default; however, you can add
    | a custom URL when required for any application testing purposes.
    |
    */

    'webhook' => env('CASHIER_WEBHOOK'),

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    |
    | This is the default currency that will be used when generating charges
    | from your application. Of course, you are welcome to use any of the
    | various world currencies that are currently supported via Paddle.
    |
    */

    'currency' => env('CASHIER_CURRENCY', 'USD'),

    /*
    |--------------------------------------------------------------------------
    | Currency Locale
    |--------------------------------------------------------------------------
    |
    | This is the default locale in which your money values are formatted in
    | for display. To utilize other locales besides the default en locale
    | verify you have the "intl" PHP extension installed on the system.
    |
    */

    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'en'),

    /*
    |--------------------------------------------------------------------------
    | Paddle Sandbox
    |--------------------------------------------------------------------------
    |
    | This option allows you to toggle between the Paddle live environment
    | and its sandboxed environment. This feature is only available for
    | a select group of vendors and not a publicly available feature.
    |
    */

    'sandbox' => env('PADDLE_SANDBOX', false),

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
                    ],
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
                    ],
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
                    ],
                ],
            ],
        ],
    ],

];
