<?php

declare(strict_types=1);

namespace App\Providers;

use App\Jobs\CreateAdminMembershipForTenant;
use App\Jobs\SeedForTenant;
use App\Jobs\SendTenantCreatedNotification;
use App\Jobs\SendWelcomeEmailSeries;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events;
use Stancl\Tenancy\Jobs;
use Stancl\Tenancy\Listeners;
use Stancl\Tenancy\Middleware;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class TenancyServiceProvider extends ServiceProvider
{
    public function events()
    {
        return [
            // Tenant events
            Events\CreatingTenant::class => [],
            Events\TenantCreated::class => [
                JobPipeline::make([
	                    SendWelcomeEmailSeries::class,
	                    SendTenantCreatedNotification::class,

						// For multi-database tenancy
	                    //Jobs\CreateDatabase::class,
	                    //Jobs\MigrateDatabase::class,
	                    //Jobs\SeedDatabase::class,

		                // For single-database tenancy
	                    SeedForTenant::class,

						CreateAdminMembershipForTenant::class,
	                ])
                    ->send(function (Events\TenantCreated $event) {
                        return $event->tenant;
                    })
                    ->shouldBeQueued(App::environment('production')),
            ],
            Events\SavingTenant::class => [],
            Events\TenantSaved::class => [],
            Events\UpdatingTenant::class => [],
            Events\TenantUpdated::class => [],
            Events\DeletingTenant::class => [],
            Events\TenantDeleted::class => [
                JobPipeline::make([
                    //Jobs\DeleteDatabase::class,
                ])
                    ->send(function (Events\TenantDeleted $event) {
                        return $event->tenant;
                    })
                    ->shouldBeQueued(App::environment('production')), // `false` by default, but you probably want to make this `true` for production.
            ],

            // Domain events
            Events\CreatingDomain::class => [],
            Events\DomainCreated::class => [],
            Events\SavingDomain::class => [],
            Events\DomainSaved::class => [],
            Events\UpdatingDomain::class => [],
            Events\DomainUpdated::class => [],
            Events\DeletingDomain::class => [],
            Events\DomainDeleted::class => [],

            // Database events
            Events\DatabaseCreated::class => [],
            Events\DatabaseMigrated::class => [],
            Events\DatabaseSeeded::class => [],
            Events\DatabaseRolledBack::class => [],
            Events\DatabaseDeleted::class => [],

            // Tenancy events
            Events\InitializingTenancy::class => [],
            Events\TenancyInitialized::class => [Listeners\BootstrapTenancy::class],

            Events\EndingTenancy::class => [],
            Events\TenancyEnded::class => [Listeners\RevertToCentralContext::class],

            Events\BootstrappingTenancy::class => [],
            Events\TenancyBootstrapped::class => [],
            Events\RevertingToCentralContext::class => [],
            Events\RevertedToCentralContext::class => [],

            // Resource syncing
            Events\SyncedResourceSaved::class => [Listeners\UpdateSyncedResource::class],

            // Fired only when a synced resource is changed in a different DB than the origin DB (to avoid infinite loops)
            Events\SyncedResourceChangedInForeignDatabase::class => [],
        ];
    }

    public function register()
    {
        //
    }

    public function boot()
    {
        $this->bootEvents();
        $this->mapRoutes();

        $this->makeTenancyMiddlewareHighestPriority();

        Middleware\InitializeTenancyBySubdomain::$onFail = static function () {
            return redirect(config('app.url'));
        };
        InitializeTenancyByDomain::$onFail = static function () {
            return redirect(config('app.url'));
        };
        Middleware\InitializeTenancyByPath::$onFail = static function() {
            abort(404);
        };
    }

    protected function bootEvents()
    {
        foreach ($this->events() as $event => $listeners) {
            foreach (array_unique($listeners) as $listener) {
                if ($listener instanceof JobPipeline) {
                    $listener = $listener->toListener();
                }

                Event::listen($event, $listener);
            }
        }
    }

    protected function mapRoutes()
    {
        if (file_exists(base_path('routes/tenant.php'))) {
            Route::namespace('')->group(base_path('routes/tenant.php'));
        }
    }

    protected function makeTenancyMiddlewareHighestPriority()
    {
        $tenancyMiddleware = [
            // Even higher priority than the initialization middleware
            Middleware\PreventAccessFromCentralDomains::class,

            InitializeTenancyByDomain::class,
            Middleware\InitializeTenancyBySubdomain::class,
            Middleware\InitializeTenancyByDomainOrSubdomain::class,
            Middleware\InitializeTenancyByPath::class,
            Middleware\InitializeTenancyByRequestData::class,
        ];

        foreach ($tenancyMiddleware as $middleware) {
            $this->app[\Illuminate\Contracts\Http\Kernel::class]->prependToMiddlewarePriority($middleware);
        }
    }
}
