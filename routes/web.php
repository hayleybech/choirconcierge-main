<?php
use App\Http\Controllers\CentralDashController;

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


Route::redirect('/', '/app');

Route::prefix('/app')->group(function () {
	Auth::routes(['register' => false]);

	Route::middleware(['auth'])->group(function () {
		Route::get('/', [CentralDashController::class, 'index'])->name('central.dash');
	});
});


