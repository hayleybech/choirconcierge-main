<?php

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


Auth::routes();

Route::get('/', function () {
	return view('welcome');
})->name('menu');
	

// Basic dashboard auth
Route::middleware('auth')->group(function() {

	Route::get('/dash', 'DashController@index')->name('dash');
	
});


// Basic employee auth
Route::middleware(['auth', 'employee'])->group(function() {

	Route::get('/singers', 'SingersController@index')->name('singers.index');
	
	Route::get('/singers/create', 'SingersController@create')->name('singer.create');
	Route::post('/singers', 'SingersController@store');
	
	Route::get('/singers/export', 'SingersController@export')->name('singers.export');

	Route::get('/singers/{singer}', 'SingersController@show')->name('singers.show');
	
	Route::get('/singers/{singer}/tasks/{task}/complete', 'SingersController@completeTask')->name('task.complete');
	
});

// Membership Team auth
Route::middleware(['auth', 'role:Membership Team'])->group(function() {
	
	Route::get('/singers/{singer}/memberprofile', function($email){
		return redirect()->away( config('app.member_profile_edit') . urlencode($email) );
	})->name('singer.memberprofile');
	
	Route::get('/memberprofile', function(){
		return redirect()->away( config('app.member_profile_new') );
	})->name('memberprofile.new');
	
	Route::get('/singers/{singer}/account/created', 'SingersController@markAccountCreated')->name('singer.account.created');
	
	Route::get('/singers/{singer}/move/archive', 'SingersController@moveToArchive')->name('singer.move.archive');
	
	
});

// Music Team auth
Route::middleware(['auth', 'role:Music Team'])->group(function() {
	
	Route::get('/singers/{singer}/voiceplacement', function($email){
		return redirect()->away( config('app.voice_placement_edit') . urlencode($email) );
	})->name('singer.voiceplacement');

	Route::get('/voiceplacement', function(){
		return redirect()->away( config('app.voice_placement_new') );
	})->name('voiceplacement.new');

	Route::get('/singers/{singer}/audition/pass', 'SingersController@auditionpass')->name('singer.audition.pass');
	
});

// Accounts Team auth
Route::middleware(['auth', 'role:Accounts Team'])->group(function() {
	
	Route::get('/singers/{singer}/fees/paid', 'SingersController@feespaid')->name('singer.fees.paid');
	
});

// Uniforms Team auth
Route::middleware(['auth', 'role:Uniforms Team'])->group(function() {
	
	Route::get('/singers/{singer}/uniform/provided', 'SingersController@markUniformProvided')->name('singer.uniform.provided');
	
});


// Admin level auth
Route::middleware(['auth', 'role:Admin'])->group(function() {
	
	Route::get('/users', 'UsersController@index')->name('users.index');

	Route::get('/users/{user}/roles/{role}/detach', 'UsersController@detachRole')->name('users.detachrole');

	Route::post('/users/{user}/role', 'UsersController@addRoles')->name('users.addroles');	
	
	// Super admin?
	
	Route::get('/migrate', function(){
		echo Artisan::call('migrate');
	});

	Route::get('/migrate/refresh', function(){
		echo Artisan::call('migrate:refresh');
	});

	Route::get('/migrate/refresh/seed', function(){
		echo Artisan::call('migrate:refresh', [
			'--seed' => true,
		]);
	});

	Route::get('/migrate/rollback', function(){
		echo Artisan::call('migrate:rollback');
	});

	Route::get('/migrate/reset', function(){
		echo Artisan::call('migrate:reset');
	});

	Route::get('/migrate/fresh', function(){
		echo Artisan::call('migrate:fresh');
	});

	Route::get('/migrate/fresh/seed', function(){
		echo Artisan::call('migrate:fresh', [
			'--seed' => true,
		]);
	});
	
});
