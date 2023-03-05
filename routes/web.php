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

// Central app will redirect to public site for now.
Route::redirect('/', 'https://www.choirconcierge.com')->name('menu');

// Public pages
//Route::view('/', 'home')->name('menu');

Auth::routes(['register' => false]);

Route::get('/', [CentralDashController::class, 'index'])->name('central.dash');
