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

// Redirect central index to folder
// choirconcierge.com/ => choirconcierge.com/app
Route::domain(config('tenancy.central_domains')[0])->group(function() {
	Route::redirect('/', '/app');
});

Route::prefix('/app')->group(function () {
	Auth::routes(['register' => false]);

	// Switch choir
	Route::get('/switch-choir/{newTenant}', [Central\SwitchTenantController::class, 'start'])->name('tenants.switch.start');

	Route::middleware(['auth'])->name('central.')->group(function () {
		Route::get('/', [Central\DashController::class, 'index'])->name('dash');
        Route::resource('default-dash', Central\DefaultDashController::class)->only(['index', 'update']);

		// Account Settings
		Route::get('account/edit', [Central\AccountController::class, 'edit'])->name('accounts.edit');
		Route::post('account', [Central\AccountController::class, 'update'])->name('accounts.update');
	});
});

