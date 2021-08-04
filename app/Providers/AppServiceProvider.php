<?php

namespace App\Providers;

use App\Http\View\Composers\SingerCategoryComposer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Auth\Guard;
use Stancl\Tenancy\Controllers\TenantAssetsController;

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

		// Get Notifications for current user, show on all pages
		/*view()->composer('*', function($view) use ($auth) {
            if($auth->check()) {
                $view->with('notifications', $auth->user()->unreadNotifications);
            }
        });*/

		View::composer('*', SingerCategoryComposer::class);

		TenantAssetsController::$tenancyMiddleware = 'Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain';

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
			$this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
		}
	}
}
