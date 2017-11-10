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
	return redirect()->away('https://docs.google.com/forms/d/e/1FAIpQLSf2auqMKw-seWTmcrnuUkwGtdG8adJ-wixC0HgsZmNVHdjWNA/viewform?entry.1045781291=' . urlencode($email) );
})->name('singer.memberprofile');

Route::get('/singers/{singer}/voiceplacement', function($email){
	return redirect()->away('https://docs.google.com/forms/d/e/1FAIpQLSezSLKjE1FinoWdOXUp58zPj9N0cGJ3Qs7FzFIQpxNwtWHgQA/viewform?entry.758241671=' . urlencode($email) );
})->name('singer.voiceplacement');

Route::get('/singers/{singer}/audition/pass', 'SingersController@auditionpass')->name('singer.audition.pass');

Auth::routes();

Route::get('/dash', 'DashController@index')->name('dash');
