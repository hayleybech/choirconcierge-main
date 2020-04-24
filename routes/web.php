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


Auth::routes(['register' => false]);

// Public pages
Route::prefix('')->group(static function (){
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

        // Complete Task
        Route::get('{singer}/tasks/{task}/complete', 'SingerController@completeTask')->name('task.complete');
    });

    // Singers - Membership Team OR SELF USER
    Route::middleware('role_or_self:Membership Team')->group(static function() {

        // Update
        Route::get('{singer}/edit', 'SingerController@edit')->name('singers.edit');
        Route::put('{singer}', 'SingerController@update')->name('singers.update');
    });

    // Singers - Membership Team
    Route::middleware('role:Membership Team')->group(static function() {

        // Create
        Route::get('create', 'SingerController@create')->name('singer.create');
        Route::post('/', 'SingerController@store');

        // Delete
        Route::get('{singer}/delete', 'SingerController@delete')->name('singer.delete');
        
        // Create Profile
        Route::get('{singer}/profile/create', 'SingerProfileController@create')->name('profile.create');
        Route::post('{singer}/profile', 'SingerProfileController@store')->name('profile');

        // Mark "Account Created" task complete
        Route::get('{singer}/account/created', 'SingerController@markAccountCreated')->name('singer.account.created');

        // Move Singer
        Route::get('{singer}/move/', 'SingerController@move')->name('singer.move');

        // Export
        Route::get('export', 'SingerController@export')->name('singers.export');
    });

    // Singers - Music Team
    Route::middleware('role:Music Team')->group(static function() {
        // Create Voice Placement
        Route::get('{singer}/placement/create', 'SingerPlacementController@create')->name('placement.create');
        Route::post('{singer}/placement', 'SingerPlacementController@store')->name('placement');

        // Mark audition passed
        Route::get('{singer}/audition/pass', 'SingerController@auditionpass')->name('singer.audition.pass');
    });

    // Singers - Accounts Team
    Route::middleware('role:Accounts Team')->group(static function() {
        // Mark fees paid
        Route::get('{singer}/fees/paid', 'SingerController@feespaid')->name('singer.fees.paid');
    });


    // Singers - Uniforms Team
    Route::middleware('role:Uniforms Team')->group(static function() {
        // Mark uniform provided
        Route::get('{singer}/uniform/provided', 'SingerController@markUniformProvided')->name('singer.uniform.provided');
    });

    // Singers - Any Authorised User
    Route::middleware('auth')->group(static function() {
        // Index
        Route::get('/', 'SingerController@index')->name('singers.index');

        // View
        Route::get('{singer}', 'SingerController@show')->name('singers.show');
    });
});


// Songs module
Route::prefix('songs')->middleware('auth')->group(static function (){

    // Any employee
    Route::middleware('employee')->group(static function() {
        // Create
        Route::get('create', 'SongController@create')->name('song.create');
        Route::post('/', 'SongController@store');

        // Edit/Delete
        Route::get('{song}/edit', 'SongController@edit')->name('song.edit');
        Route::put('{song}', 'SongController@update');
        Route::get('{song}/delete', 'SongController@delete')->name('song.delete');

        // Create/Delete attachments
        Route::post('{song}/attachments', 'SongAttachmentController@store')->name('song.attachments.store');
        Route::get('{song}/attachments/{attachment}/delete', 'SongAttachmentController@delete')->name('song.attachments.delete');
    });

    // Any Authorised User
    Route::middleware('auth')->group(static function() {
        // Index
        Route::get('/', 'SongController@index')->name('songs.index');

        // Learning Mode
        Route::get('learning', 'SongController@learning')->name('songs.learning');

        // View
        Route::get('{song}', 'SongController@show')->name('songs.show');

    });
});

// Events module
Route::prefix('events')->group(static function (){

    /// Any Employee
    Route::middleware('employee')->group(static function() {
        // Create
        Route::get('create', 'EventController@create')->name('event.create');
        Route::post('/', 'EventController@store');

        // Edit/Delete
        Route::get('{event}/edit', 'EventController@edit')->name('event.edit');
        Route::put('{event}', 'EventController@update');
        Route::get('{event}/delete', 'EventController@delete')->name('event.delete');
    });

    // Any Authorised User
    Route::middleware('auth')->group(static function() {
        // Index
        Route::get('/', 'EventController@index')->name('events.index');

        //View
        Route::get('{event}', 'EventController@show')->name('events.show');
    });
});

// Notifications module
Route::prefix('notifications')->name('notifications')->middleware(['auth', 'employee'])->group(static function (){
    // Index - BROKEN
    Route::resource('/', 'NotificationController');
});


// Users/Team module
Route::prefix('users')->middleware(['auth', 'role:Admin'])->group(static function () {
    // Index
    Route::get('/', 'UserController@index')->name('users.index');

    // AJAX Search
    Route::get('/find', 'UserController@findUsers');
    Route::get('/roles/find', 'UserController@findRoles');

    // Attach/Detach role from a user
    Route::get('{user}/roles/{role}/detach', 'UserController@detachRole')->name('users.detachrole');
    Route::post('{user}/role', 'UserController@addRoles')->name('users.addroles');
});

// Mailing Lists (User Groups) module
Route::middleware(['auth', 'role:Admin'])->group(static function () {
    Route::resource('groups', 'UserGroupController');

});

/** Mailbox **/
Route::get('/mailbox/process', 'MailboxController@process');

// Tasks module
Route::prefix('tasks')->middleware(['auth', 'role:Admin'])->group(static function () {
    // Index
    Route::get('/', 'TaskController@index')->name('tasks.index');
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
    Route::get('import', 'SingerController@import');
});


// Public calendar feed
Route::get('/events-ical', 'ICalController@index');