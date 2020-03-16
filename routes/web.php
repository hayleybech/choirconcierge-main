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

// Primary pages
Route::prefix('')->middleware('auth')->group(static function (){
    // Home page
    Route::get('/', static function () {
        return view('welcome');
    })->name('menu');

});

// Dashboard
Route::prefix('dash')->middleware('auth')->group(static function (){
    // Index
    Route::get('/', 'DashController@index')->name('dash');
});

// Singers module
Route::prefix('singers')->middleware('auth')->group(static function (){

    // Singers - Any Employee
    Route::middleware('employee')->group(static function() {
        // Index
        Route::get('/', 'SingersController@index')->name('singers.index');

        // Create
        Route::get('create', 'SingersController@create')->name('singer.create');
        Route::post('/', 'SingersController@store');

        // View/Update/Delete
        Route::get('{singer}', 'SingersController@show')->name('singers.show');
        Route::get('{singer}/edit', 'SingersController@edit')->name('singers.edit');
        Route::put('{singer}', 'SingersController@update')->name('singers.update');
        Route::get('{singer}/delete', 'SingersController@delete')->name('singer.delete');

        // Complete Task
        Route::get('{singer}/tasks/{task}/complete', 'SingersController@completeTask')->name('task.complete');

        // Export
        Route::get('export', 'SingersController@export')->name('singers.export');
    });

    // Singers - Membership Team
    Route::middleware('role:Membership Team')->group(static function() {
        // Create Profile
        Route::get('{singer}/profile/create', 'SingersController@createProfile')->name('profile.create');
        Route::post('{singer}/profile', 'SingersController@storeProfile')->name('profile');

        // Mark "Account Created" task complete
        Route::get('{singer}/account/created', 'SingersController@markAccountCreated')->name('singer.account.created');

        // Move Singer
        Route::get('{singer}/move/', 'SingersController@move')->name('singer.move');
    });

    // Singers - Music Team
    Route::middleware('role:Music Team')->group(static function() {
        // Create Voice Placement
        Route::get('{singer}/placement/create', 'SingersController@createPlacement')->name('placement.create');
        Route::post('{singer}/placement', 'SingersController@storePlacement')->name('placement');

        // Mark audition passed
        Route::get('{singer}/audition/pass', 'SingersController@auditionpass')->name('singer.audition.pass');
    });

    // Singers - Accounts Team
    Route::middleware('role:Accounts Team')->group(static function() {
        // Mark fees paid
        Route::get('{singer}/fees/paid', 'SingersController@feespaid')->name('singer.fees.paid');
    });


    // Singers - Uniforms Team
    Route::middleware('role:Uniforms Team')->group(static function() {
        // Mark uniform provided
        Route::get('{singer}/uniform/provided', 'SingersController@markUniformProvided')->name('singer.uniform.provided');
    });
});


// Songs module
Route::prefix('songs')->middleware('auth')->group(static function (){
    // Index
    Route::get('/', 'SongsController@index')->name('songs.index');

    // Create
    Route::get('create', 'SongsController@create')->name('song.create');
    Route::post('/', 'SongsController@store');

    // View/Edit/Delete
    Route::get('{song}', 'SongsController@show')->name('songs.show');
    Route::get('{song}/edit', 'SongsController@edit')->name('song.edit');
    Route::put('{song}', 'SongsController@update');
    Route::get('{song}/delete', 'SongsController@delete')->name('song.delete');

    // Create/Delete attachments
    Route::post('{song}/attachments', 'SongAttachmentController@store')->name('song.attachments.store');
    Route::get('{song}/attachments/{attachment}/delete', 'SongAttachmentController@delete')->name('song.attachments.delete');

    // Access Learning Mode
    Route::get('learning', 'SongsController@learning')->name('songs.learning');
});

// Events module
Route::prefix('events')->middleware('auth')->group(static function (){
    // Index
    Route::get('/', 'EventsController@index')->name('events.index');

    // Create
    Route::get('create', 'EventsController@create')->name('event.create');
    Route::post('/', 'EventsController@store');

    // View/Edit/Delete
    Route::get('{event}', 'EventsController@show')->name('events.show');
    Route::get('{event}/edit', 'EventsController@edit')->name('event.edit');
    Route::put('{event}', 'EventsController@update');
    Route::get('{event}/delete', 'EventsController@delete')->name('event.delete');
});

// Notifications module
Route::prefix('notifications')->name('notifications')->middleware(['auth', 'employee'])->group(static function (){
    // Index - BROKEN
    Route::resource('/', 'NotificationController');
});


// Users/Team module
Route::prefix('users')->middleware(['auth', 'role:Admin'])->group(static function () {
    // Index
    Route::get('/', 'UsersController@index')->name('users.index');

    // Attach/Detach role from a user
    Route::get('{user}/roles/{role}/detach', 'UsersController@detachRole')->name('users.detachrole');
    Route::post('{user}/role', 'UsersController@addRoles')->name('users.addroles');
});

// User Groups module
Route::prefix('groups')->name('groups.')->middleware(['auth', 'role:Admin'])->group(static function () {
    Route::resource('/', 'UserGroupController');
});

// Tasks module
Route::prefix('tasks')->middleware(['auth', 'role:Admin'])->group(static function () {
    // Index
    Route::get('/', 'TasksController@index')->name('tasks.index');
});

// Notification Templates module
Route::prefix('notification-templates')->name('notification-templates.')->middleware(['auth', 'role:Admin'])->group(static function () {
    // Index
    Route::resource('/', 'NotificationTemplateController');

});

// Migrations
// @todo Create a super admin role
Route::prefix('migrate')->middleware(['auth', 'role:Admin'])->group(static function(){

    // migrate
    Route::get('/', function(){
        echo Artisan::call('migrate', [
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // migrate:status
    Route::get('/status', function(){
        echo Artisan::call('migrate:status');
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // migrate:refresh
    Route::get('refresh', static function(){
        echo Artisan::call('migrate:refresh');
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // migrate:refresh --seed
    Route::get('refresh/seed', static function(){
        echo Artisan::call('migrate:refresh', [
            '--seed' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // migrate:rollback
    Route::get('rollback', static function(){
        echo Artisan::call('migrate:rollback');
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // migrate:reset
    Route::get('reset', static function(){
        echo Artisan::call('migrate:reset');
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // migrate:fresh
    Route::get('fresh', static function(){
        echo Artisan::call('migrate:fresh');
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // migrate:fresh --seed
    Route::get('fresh/seed', static function(){
        echo Artisan::call('migrate:fresh', [
            '--seed' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });
});

// Database Seeds
// @todo Create a super admin role
// @todo Group seeds into production-safe and non-production safe
Route::prefix('seed')->middleware(['auth', 'role:Admin'])->group(static function(){

    // DO NOT RUN ON PRODUCTION
    // Seed Users
    // db:seed --class=UserTableSeeder
    Route::get('users', function(){
        echo Artisan::call('db:seed', ['--class' => 'UserTableSeeder']);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Seed Singers
    // db:seed --class=SingerTableSeeder
    Route::get('singers', function(){
        echo Artisan::call('db:seed', [
            '--class' => 'SingerTableSeeder',
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // ONLY RUN ONCE
    // Seed Singer Categories
    // db:seed --class=SingerCategorySeeder
    Route::get('singer-categories', function(){
        echo Artisan::call('db:seed', [
            '--class' => 'SingerCategorySeeder',
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Seed Tasks
    // db:seed --class=TaskTableSeeder
    Route::get('tasks', function(){
        echo Artisan::call('db:seed', [
            '--class' => 'TaskTableSeeder',
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Seed Templates
    // db:seed --class=NotificationTemplateSeeder
    Route::get('templates', function(){
        echo Artisan::call('db:seed', [
            '--class' => 'NotificationTemplateSeeder',
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Singer import
    Route::get('import', 'SingersController@import');
});


// Public calendar feed
Route::get('/events-ical', 'ICalController@index');