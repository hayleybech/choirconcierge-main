<?php

namespace App\Providers;

use App\Http\View\Composers\SingerCategoryComposer;
use Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Stancl\Tenancy\Controllers\TenantAssetsController;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {
        Model::preventLazyLoading($this->app->environment(['local', 'testing']));

        Schema::defaultStringLength(191);

        View::composer('*', SingerCategoryComposer::class);

        TenantAssetsController::$tenancyMiddleware = InitializeTenancyByDomainOrSubdomain::class;

        Paginator::useBootstrap();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(SingerCategoryComposer::class);

        if ($this->app->environment() !== 'production') {
            $this->app->register(IdeHelperServiceProvider::class);
        }
    }
}
