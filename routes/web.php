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

Route::get('/', function () {
	return view('welcome');
})->name('menu');

Route::get('/singers', 'SingersController@index')->name('singers.index');

	Route::get('/singers/{singer}', 'SingersController@show')->name('singers.show');

	Route::get('/singers/{singer}/memberprofile', function($email){
		return redirect()->away( config('app.member_profile_edit') . urlencode($email) );
	})->name('singer.memberprofile');

	Route::get('/singers/{singer}/voiceplacement', function($email){
		return redirect()->away( config('app.voice_placement_edit') . urlencode($email) );
	})->name('singer.voiceplacement');

	Route::get('/memberprofile', function(){
		return redirect()->away( config('app.member_profile_new') );
	})->name('memberprofile.new');

	Route::get('/voiceplacement', function(){
		return redirect()->away( config('app.voice_placement_new') );
	})->name('voiceplacement.new');

	Route::get('/singers/{singer}/audition/pass', 'SingersController@auditionpass')->name('singer.audition.pass');

Auth::routes();

Route::get('/dash', 'DashController@index')->name('dash');

Route::get('/users', 'UsersController@index')->name('users.index');

//Route::post('/users/{user}/roles', 'UsersController@addRoles')->name('users.addroles');
