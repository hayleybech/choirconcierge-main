<?php

use App\Http\Controllers\Central;
use App\Http\Middleware\EnsureUserIsSuperAdmin;

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

Route::redirect('/', config('app.public_site_url'));

Route::prefix('/app')->group(function () {
	Auth::routes();

    // Two-Factor Authentication Challenge
    Route::get('/two-factor-challenge', [App\Http\Controllers\Auth\TwoFactorChallengeController::class, 'create'])->name('auth.2fa.challenge');
    Route::post('/two-factor-challenge', [App\Http\Controllers\Auth\TwoFactorChallengeController::class, 'store']);

    Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])->name('login');

	// Switch organisation
	Route::get('/switch-choir/{newTenant}', [Central\SwitchTenantController::class, 'start'])->name('tenants.switch.start');

	Route::middleware(['auth'])->name('central.')->group(function () {
		Route::get('/', [Central\DashController::class, 'index'])->name('dash');
        Route::resource('default-dash', Central\DefaultDashController::class)->only(['index', 'update', 'destroy']);
        Route::resource('mail-logs', Central\MailLogController::class)->only(['index', 'show'])->middleware([EnsureUserIsSuperAdmin::class]);

        // Tenants
        Route::resource('tenants', Central\TenantController::class)->only(['index', 'show', 'create', 'store']);
        Route::get('tenants/{tenant}/onboarding', Central\TenantOnboardingController::class)->name('tenants.onboarding');
        Route::get('tenants/{tenant}/track-demo', Central\TrackTenantSalesDemoController::class)->name('tenants.track-demo');
        Route::get('tenants/{tenant}/trial', [Central\TenantTrialController::class, 'update'])->name('tenants.trial.update');

        // Users
        Route::resource('users', Central\UserController::class)->only(['index', 'show']);

		// Account Settings
		Route::singleton('account', Central\AccountController::class)->only(['edit', 'update']);

        // Two-Factor Authentication
        Route::name('account.')->prefix('account')->group(function () {
            Route::post('/two-factor/regenerate', [App\Http\Controllers\TwoFactorController::class, 'regenerate'])->name('two-factor.regenerate');
            Route::singleton('two-factor', App\Http\Controllers\TwoFactorController::class)->creatable()->only(['show', 'store', 'destroy']);
        });
//        Route::get('account/two-factor', [App\Http\Controllers\TwoFactorController::class, 'index'])->name('accounts.two-factor');
//        Route::post('account/two-factor', [App\Http\Controllers\TwoFactorController::class, 'store']);
//        Route::delete('account/two-factor', [App\Http\Controllers\TwoFactorController::class, 'destroy']);

        // Roadmap
        Route::get('changelog', Central\ChangelogController::class)->name('changelog');
	});
});

