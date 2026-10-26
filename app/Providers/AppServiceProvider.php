<?php

namespace App\Providers;

use App\Features\InboxFeature;
use App\Http\View\Composers\SingerStatusComposer;
use App\Models\Billing\Customer as BillingCustomer;
use App\Models\Billing\Subscription as BillingSubscription;
use App\Models\Billing\SubscriptionItem as BillingSubscriptionItem;
use App\Models\Billing\Transaction as BillingTransaction;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Paddle\Cashier;
use Stancl\Tenancy\Controllers\TenantAssetsController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Cashier::useCustomerModel(BillingCustomer::class);
        Cashier::useSubscriptionModel(BillingSubscription::class);
        Cashier::useSubscriptionItemModel(BillingSubscriptionItem::class);
        Cashier::useTransactionModel(BillingTransaction::class);

        InboxFeature::define();

        Model::preventLazyLoading($this->app->environment(['local', 'testing']));

        Schema::defaultStringLength(191);

        View::composer('*', SingerStatusComposer::class);

        TenantAssetsController::$tenancyMiddleware = InitializeTenancyByDomainOrSubdomain::class;

        Paginator::useBootstrap();

        if(App::isLocal()) {
            URL::forceRootUrl(config('app.url'));
        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SingerStatusComposer::class);

        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
