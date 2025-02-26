<?php

use App\Http\Controllers\Central;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Redirect root to marketing site (only run app in /app)
Route::domain(config('tenancy.central_domains')[0])->group(function() {
	Route::redirect('/', config('app.public_site_url'));
});

Route::prefix('/app')->group(function () {
	Auth::routes();

	// Switch organisation
	Route::get('/switch-choir/{newTenant}', [Central\SwitchTenantController::class, 'start'])->name('tenants.switch.start');

	Route::middleware(['auth'])->name('central.')->group(function () {
		Route::get('/', [Central\DashController::class, 'index'])->name('dash');
        Route::resource('default-dash', Central\DefaultDashController::class)->only(['index', 'update', 'destroy']);
        Route::resource('tenants', Central\TenantController::class)->only(['index', 'show', 'create', 'store']);
        Route::get('tenants/{tenant}/onboarding', Central\TenantOnboardingController::class)->name('tenants.onboarding');
        Route::get('tenants/{tenant}/track-demo', Central\TrackTenantSalesDemoController::class)->name('tenants.track-demo');

		// Account Settings
		Route::get('account/edit', [Central\AccountController::class, 'edit'])->name('accounts.edit');
		Route::post('account', [Central\AccountController::class, 'update'])->name('accounts.update');
	});
});

