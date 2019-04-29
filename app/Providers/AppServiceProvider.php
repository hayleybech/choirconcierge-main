<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Contracts\Auth\Guard;
use App\SingerCategory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {
		Schema::defaultStringLength(191);

		// Get Notifications for current user, show on all pages
        view()->composer('*', function($view) use ($auth) {
            if($auth->check()) {
                $view->with('notifications', $auth->user()->unreadNotifications);
            }
        });

        // Get list of singer categories
        view()->composer('*', function($view) use ($auth) {
            $categories = SingerCategory::all();
            $categories_move = $categories->mapWithKeys(function($item){
                return [ $item['id'] => $item['name'] ];
            });
            $categories_move->prepend('Select a Category', 0);

            $view->with('categories_move', $categories_move);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }
    }
}
