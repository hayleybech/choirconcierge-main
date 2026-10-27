<?php

namespace ChoirConcierge\LegacySpark;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use ChoirConcierge\LegacyCashierPaddle\Cashier;
use ChoirConcierge\LegacyCashierPaddle\Receipt;
use Money\Currencies\ISOCurrencies;
use Money\Currency;
use Money\Parser\IntlLocalizedDecimalParser;
use NumberFormatter;

class FrontendState
{
    /**
     * Get the data should be shared with the frontend.
     *
     * @param  string  $type
     * @return array
     */
    public function current($type, Model $billable)
    {
        /** @var \ChoirConcierge\LegacyCashierPaddle\Subscription */
        $subscription = $billable->subscription();

        $plans = static::getPlans($type, $billable);

        $plan = $subscription && $subscription->active()
            ? $plans->firstWhere('id', $subscription->paddle_plan)
            : null;

        $lastPayment = self::subscriptionIsActive($subscription) || self::subscriptionIsPastDue($subscription)
            ? optional($subscription->lastPayment())->toArray()
            : null;

        if ($lastPayment) {
            $lastPayment['date'] = Carbon::make($lastPayment['date'])->format(config('spark.date_format', 'F j, Y'));
        }

        $nextPayment = self::subscriptionIsActive($subscription) || self::subscriptionIsPastDue($subscription)
            ? optional($subscription->nextPayment())->toArray()
            : null;

        if ($nextPayment) {
            $nextPayment['date'] = Carbon::make($nextPayment['date'])->translatedFormat(config('spark.date_format', 'F j, Y'));
        }

        $user = Auth::user();

        return [
            'appLogo' => static::logo(),
            'appName' => config('app.name', 'Laravel'),
            'sandbox' => config('cashier_classic.sandbox'),
            'billableId' => (string) $billable->id,
            'billableName' => $billable->name,
            'billableType' => $type,
            'brandColor' => $this->brandColor(),
            'cardBrand' => static::cardBrand($subscription),
            'cardExpirationDate' => static::cardExpiration($subscription),
            'cardLastFour' => static::cardLastFour($subscription),
            'dashboardUrl' => static::dashboardUrl(),
            'defaultInterval' => config('spark.billables.'.$type.'.default_interval', 'monthly'),
            'genericTrialEndsAt' => $billable->onGenericTrial() ? $billable->customer->trial_ends_at->translatedFormat(config('spark.date_format', 'F j, Y')) : null,
            'lastPayment' => $lastPayment,
            'message' => request('message', ''),
            'monthlyPlans' => $plans->where('interval', 'monthly')->where('active', true)->values(),
            'nextPayment' => $nextPayment,
            'paddleVendorId' => (int) config('cashier.vendor_id'),
            'paymentMethod' => self::subscriptionIsActive($subscription) || self::subscriptionIsPastDue($subscription) ? $subscription->paymentMethod() : null,
            'plan' => $plan,
            'receipts' => static::receipts($billable),
            'seatName' => Spark::seatName($type),
            'state' => static::state($billable, $subscription),
            'termsUrl' => $this->termsUrl(),
            'userAvatar' => isset($user['profile_photo_url']) ? $user->profile_photo_url : null,
            'userName' => $user->name,
            'yearlyPlans' => $plans->where('interval', 'yearly')->where('active', true)->values(),
        ];
    }

    /**
     * Get the logo that is configured for the billing portal.
     *
     * @return string|null
     */
    protected static function logo()
    {
        $logo = config('spark.brand.logo');

        if (! empty($logo) && file_exists(realpath($logo))) {
            return file_get_contents(realpath($logo));
        }

        return $logo;
    }

    /**
     * Get the brand color for the application.
     *
     * @return string
     */
    protected function brandColor()
    {
        $color = config('spark.brand.color', 'bg-gray-800');

        return strpos($color, '#') === 0 ? 'bg-custom-hex' : $color;
    }

    /**
     * Get the subscription plans.
     *
     * @param  string  $type
     * @param  \Illuminate\Database\Eloquent\Model  $billable
     * @return \Illuminate\Support\Collection
     */
    protected function getPlans($type, $billable)
    {
        $plans = Spark::plans($type);

        $paddlePlans = $this->getProductPrices($plans);

        foreach ($paddlePlans as $plan) {
            if ($sparkPlan = $plans->where('id', $plan->product_id)->first()) {
                $priceKey = $plan->vendor_set_prices_included_tax ? 'gross' : 'net';

                $price = static::parseAmount(
                    (string) $plan->recurringPrice()->{$priceKey},
                    $plan->currency
                );

                $formattedPrice = Cashier::formatAmount((int) $price, $plan->currency);

                if (Str::endsWith($formattedPrice, '.00')) {
                    $formattedPrice = substr($formattedPrice, 0, -3);
                }

                if (Str::endsWith($formattedPrice, '.0')) {
                    $formattedPrice = substr($formattedPrice, 0, -2);
                }

                $sparkPlan->price = $formattedPrice;

                $sparkPlan->priceIncludesVat = $plan->vendor_set_prices_included_tax;

                $sparkPlan->currency = $plan->currency;
            }
        }

        return $plans;
    }

    /**
     * Get the product prices from Paddle.
     *
     * @param  \Illuminate\Support\Collection  $plans
     * @return \Illuminate\Support\Collection
     */
    protected function getProductPrices($plans)
    {
        return Cashier::productPrices($plans->map->id->toArray(), [
            'customer_ip' => request()->ip(),
        ]);
    }

    /**
     * Get the current subscription state.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @return string
     */
    protected static function state(Model $billable, $subscription)
    {
        if (static::pendingCheckout($billable, $subscription)) {
            return 'pending';
        }

        if (static::hasHighRiskPayment($billable)) {
            return 'hasHighRiskPayment';
        }

        if ($subscription && $subscription->onPausedGracePeriod()) {
            return 'onGracePeriod';
        }

        if (self::subscriptionIsPastDue($subscription)) {
            return 'past_due';
        }

        if (self::subscriptionIsActive($subscription)) {
            return 'active';
        }

        return 'none';
    }

    /**
     * Determine if the subscription is in an "active" state.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @return bool
     */
    protected static function subscriptionIsActive($subscription)
    {
        return $subscription &&
               $subscription->active() &&
               ! $subscription->onGracePeriod();
    }

    /**
     * Determine if the subscription is in a "past_due" state.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @return bool
     */
    protected static function subscriptionIsPastDue($subscription)
    {
        return $subscription &&
               $subscription->pastDue();
    }

    /**
     * Determine if we are waiting for webhooks to arrive.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @return bool
     */
    protected static function pendingCheckout(Model $billable, $subscription)
    {
        if (self::subscriptionIsActive($subscription) || self::subscriptionIsPastDue($subscription)) {
            $billable->customer->update([
                'pending_checkout_id' => null,
            ]);

            return false;
        }

        return
            $billable->customer &&
            $billable->customer->pending_checkout_id;
    }

    /**
     * Determine if the billable has a high risk payment and is under verification.
     *
     * @return bool
     */
    protected static function hasHighRiskPayment(Model $billable)
    {
        return
            $billable->customer &&
            $billable->customer->has_high_risk_payment;
    }

    /**
     * List all receipts of the given billable.
     *
     * @return array
     */
    protected static function receipts(Model $billable)
    {
        return $billable->receipts()
            ->where('amount', '>', 0)
            ->paginate(10)
            ->withQueryString()
            ->through(function (Receipt $receipt) {
                $amount = static::parseAmount(
                    (string) $receipt->amount,
                    $receipt->currency
                );

                $receipt->amount = Cashier::formatAmount($amount, $receipt->currency);

                $receipt = $receipt->toArray();

                $receipt['paid_at'] = Carbon::make($receipt['paid_at'])->translatedFormat(config('spark.date_format', 'F j, Y'));

                return $receipt;
            });
    }

    /**
     * Get the card brand.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @return string|null
     */
    protected static function cardBrand($subscription)
    {
        if (! self::subscriptionIsActive($subscription) && ! self::subscriptionIsPastDue($subscription)) {
            return;
        }

        return $subscription->paymentMethod() == 'card'
            ? $subscription->cardBrand()
            : 'paypal';
    }

    /**
     * Get the card expiration.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @return string|null
     */
    protected static function cardExpiration($subscription)
    {
        if (! self::subscriptionIsActive($subscription) && ! self::subscriptionIsPastDue($subscription)) {
            return;
        }

        return $subscription->paymentMethod() == 'card'
            ? $subscription->cardExpirationDate()
            : '';
    }

    /**
     * Get the card last 4 digits.
     *
     * @param  \ChoirConcierge\LegacyCashierPaddle\Subscription  $subscription
     * @return string|null
     */
    protected static function cardLastFour($subscription)
    {
        if (! self::subscriptionIsActive($subscription) && ! self::subscriptionIsPastDue($subscription)) {
            return;
        }

        return $subscription->paymentMethod() == 'card'
            ? $subscription->cardLastFour()
            : '';
    }

    /**
     * Get the URL of the billing dashboard.
     *
     * @return string
     */
    protected static function dashboardUrl()
    {
        if ($dashboardUrl = config('spark.dashboard_url')) {
            return $dashboardUrl;
        }

        return app('router')->has('dashboard') ? route('dashboard') : '/';
    }

    /**
     * Get the URL of the "terms of service" page.
     *
     * @return string
     */
    protected function termsUrl()
    {
        if ($termsUrl = config('spark.terms_url')) {
            return $termsUrl;
        }

        return app('router')->has('terms.show') ? route('terms.show') : null;
    }

    /**
     * Parse the given amount from Paddle.
     *
     * @param  string  $amount
     * @param  string  $currency
     * @return string
     */
    protected static function parseAmount($amount, $currency)
    {
        $currencies = new ISOCurrencies();

        $numberFormatter = new NumberFormatter('en', NumberFormatter::DECIMAL);

        $moneyParser = new IntlLocalizedDecimalParser($numberFormatter, $currencies);

        $money = $moneyParser->parse($amount, new Currency(strtoupper($currency ?? config('cashier.currency'))));

        return $money->getAmount();
    }
}
