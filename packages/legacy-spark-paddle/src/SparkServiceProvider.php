<?php

namespace ChoirConcierge\LegacySpark;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use ChoirConcierge\LegacyCashierPaddle\Cashier;
use ChoirConcierge\LegacySpark\Contracts\Actions\GeneratesSubscriptionPayLinks;

class SparkServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        Cashier::ignoreMigrations();

        if (! $this->app->configurationIsCached()) {
            $this->mergeConfigFrom(__DIR__.'/../config/spark.php', 'spark');
        }

        $this->app->singleton('spark.manager', SparkManager::class);
        $this->app->singleton(FrontendState::class);

        app()->singleton(
            GeneratesSubscriptionPayLinks::class,
            Actions\GenerateSubscriptionPayLink::class
        );

        $this->registerCommands();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'spark');

        $this->configureRoutes();
        $this->configureMigrations();
        $this->configureTranslations();
        $this->configurePublishing();
    }

    /**
     * Register the routes offered by the application.
     *
     * @return void
     */
    protected function configureRoutes()
    {
        Route::group([], function () {
            $this->loadRoutesFrom(__DIR__.'/../routes/routes.php');
        });
    }

    /**
     * Configure Spark migrations.
     *
     * @return void
     */
    protected function configureMigrations()
    {
        if (Spark::runsMigrations() && $this->app->runningInConsole()) {
            $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
        }
    }

    /**
     * Configure Spark translations.
     *
     * @return void
     */
    protected function configureTranslations()
    {
        $this->loadJsonTranslationsFrom(lang_path('spark'));
    }

    /**
     * Configure publishing for the package.
     *
     * @return void
     */
    protected function configurePublishing()
    {
        if (! $this->app->runningInConsole()) {
            return;
        }

        $this->publishes([
            __DIR__.'/../config/spark.php' => config_path('spark.php'),
        ], 'spark-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/spark'),
        ], 'spark-views');

        $this->publishes([
            __DIR__.'/../stubs/en.json' => lang_path('spark/en.json'),
        ], 'spark-lang');

        $this->publishes([
            __DIR__.'/../stubs/SparkServiceProvider.php' => app_path('Providers/SparkServiceProvider.php'),
        ], 'spark-provider');

        $this->publishes([
            __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'spark-migrations');
    }

    /**
     * Register the Spark Artisan commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }
}
