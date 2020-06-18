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


use App\Http\Controllers\DashController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ICalController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\NotificationTemplateController;
use App\Http\Controllers\RiserStackController;
use App\Http\Controllers\SingerController;
use App\Http\Controllers\SingerPlacementController;
use App\Http\Controllers\SingerProfileController;
use App\Http\Controllers\SongAttachmentController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;

Auth::routes(['register' => false]);

// Public pages
Route::view('welcome', 'welcome');

// Dashboard
Route::prefix('dash')->middleware('auth')->group(static function (){
    // Index
    Route::get('/', [DashController::class, 'index'])->name('dash');
});

// Singers module
Route::prefix('singers')->middleware('auth')->group(static function (){

    // Singers - Any Employee
    Route::middleware('employee')->group(static function() {

        // Complete Task
        Route::get('{singer}/tasks/{task}/complete', [SingerController::class, 'completeTask'])->name('task.complete');
    });

    // Singers - Membership Team OR SELF USER
    Route::middleware('role_or_self:Membership Team')->group(static function() {

        // Update
        Route::get('{singer}/edit', [SingerController::class, 'edit'])->name('singers.edit');
        Route::put('{singer}', [SingerController::class, 'update'])->name('singers.update');
    });

    // Singers - Membership Team
    Route::middleware('role:Membership Team')->group(static function() {

        // Create
        Route::get('create', [SingerController::class, 'create'])->name('singers.create');
        Route::post('/', [SingerController::class, 'store']);

        // Delete
        Route::get('{singer}/delete', [SingerController::class, 'delete'])->name('singers.delete');
        
        // Create Profile
        Route::get('{singer}/profile/create', [SingerProfileController::class, 'create'])->name('profile.create');
        Route::post('{singer}/profile', [SingerProfileController::class, 'store'])->name('profile');
        Route::get('{singer}/profile/{profile}/edit', [SingerProfileController::class, 'edit'])->name('profiles.edit');
        Route::put('{singer}/profile/{profile}', [SingerProfileController::class, 'update'])->name('profiles.update');

        // Mark "Account Created" task complete
        Route::get('{singer}/account/created', [SingerController::class, 'markAccountCreated'])->name('singers.account.created');

        // Move Singer
        Route::get('{singer}/move/', [SingerController::class ,'move'])->name('singers.move');

        // Export
        Route::get('export', [SingerController::class, 'export'])->name('singers.export');
    });

    // Singers - Music Team
    Route::middleware('role:Music Team')->group(static function() {
        // Create Voice Placement
        Route::get('{singer}/placement/create', [SingerPlacementController::class, 'create'])->name('placement.create');
        Route::post('{singer}/placement', [SingerPlacementController::class, 'store'])->name('placement');
        Route::get('{singer}/placement/{placement}/edit', [SingerPlacementController::class, 'edit'])->name('placements.edit');
        Route::put('{singer}/placement/{placement}', [SingerPlacementController::class, 'update'])->name('placements.update');

        // Mark audition passed
        Route::get('{singer}/audition/pass', [SingerController::class, 'auditionpass'])->name('singers.audition.pass');
    });

    // Singers - Accounts Team
    Route::middleware('role:Accounts Team')->group(static function() {
        // Mark fees paid
        Route::get('{singer}/fees/paid', [SingerController::class, 'feespaid'])->name('singers.fees.paid');
    });


    // Singers - Uniforms Team
    Route::middleware('role:Uniforms Team')->group(static function() {
        // Mark uniform provided
        Route::get('{singer}/uniform/provided', [SingerController::class, 'markUniformProvided'])->name('singers.uniform.provided');
    });

    // Singers - Any Authorised User
    Route::middleware('auth')->group(static function() {
        // Index
        Route::get('/', [SingerController::class, 'index'])->name('singers.index');

        // View
        Route::get('{singer}', [SingerController::class, 'show'])->name('singers.show');
    });
});


// Songs module
Route::prefix('songs')->middleware('auth')->group(static function (){

    // Any employee
    Route::middleware('employee')->group(static function() {
        // Create
        Route::get('create', [SongController::class, 'create'])->name('songs.create');
        Route::post('/', [SongController::class, 'store']);

        // Edit/Delete
        Route::get('{song}/edit', [SongController::class, 'edit'])->name('songs.edit');
        Route::put('{song}', [SongController::class, 'update']);
        Route::get('{song}/delete', [SongController::class, 'delete'])->name('songs.delete');

        // Create/Download/Delete attachments
        Route::post('{song}/attachments', [SongAttachmentController::class, 'store'])->name('songs.attachments.store');
        Route::get('{song}/attachments/{attachment}', [SongAttachmentController::class, 'show'])->name('songs.attachments.show');
        Route::get('{song}/attachments/{attachment}/delete', [SongAttachmentController::class, 'delete'])->name('songs.attachments.delete');
    });

    // Any Authorised User
    Route::middleware('auth')->group(static function() {
        // Index
        Route::get('/', [SongController::class, 'index'])->name('songs.index');

        // Learning Mode
        Route::get('learning', [SongController::class, 'learning'])->name('songs.learning');

        // View
        Route::get('{song}', [SongController::class, 'show'])->name('songs.show');

    });
});

// Events module
Route::prefix('events')->group(static function (){

    /// Any Employee
    Route::middleware('employee')->group(static function() {
        // Create
        Route::get('create', [EventController::class, 'create'])->name('events.create');
        Route::post('/', [EventController::class, 'store']);

        // Edit/Delete
        Route::get('{event}/edit', [EventController::class, 'edit'])->name('events.edit');
        Route::put('{event}', [EventController::class, 'update']);
        Route::get('{event}/delete', [EventController::class, 'delete'])->name('events.delete');
    });

    // Any Authorised User
    Route::middleware('auth')->group(static function() {
        // Index
        Route::get('/', [EventController::class, 'index'])->name('events.index');

        //View
        Route::get('{event}', [EventController::class, 'show'])->name('events.show');
    });
});

// Documents module
Route::prefix('folders')->group(static function (){

    Route::middleware(['auth', 'employee'])->group(static function() {
        // Create/Delete documents
        Route::post('{folder}/documents', [DocumentController::class, 'store'])->name('folders.documents.store');
        Route::get('{folder}/documents/{document}/delete', [DocumentController::class, 'destroy'])->name('folders.documents.delete');
    });
});
Route::resource('folders', 'FolderController');

// Risers module
Route::resource('stacks', 'RiserStackController');

// Notifications module
Route::prefix('notifications')->name('notifications')->middleware(['auth', 'employee'])->group(static function (){
    // Index - BROKEN
    Route::resource('/', 'NotificationController');
});


// Users/Team module
Route::prefix('users')->middleware(['auth', 'role:Admin'])->group(static function () {
    // Index
    Route::get('/', [UserController::class, 'index'])->name('users.index');

    // AJAX Search
    Route::get('/find', [UserController::class, 'findUsers']);
    Route::get('/roles/find', [UserController::class, 'findRoles']);
    Route::get('/voice-parts/find', [UserController::class, 'findVoiceParts']);
    Route::get('/singer-categories/find', [UserController::class, 'findSingerCategories']);

    // Attach/Detach role from a user
    Route::get('{user}/roles/{role}/detach', [UserController::class, 'detachRole'])->name('users.detachrole');
    Route::post('{user}/role', [UserController::class, 'addRoles'])->name('users.addroles');
});

// Mailing Lists (User Groups) module
Route::resource('groups', 'UserGroupController')->middleware(['auth', 'role:Admin']);

/** Mailbox **/
Route::get('/mailbox/process', [MailboxController::class, 'process']);

// Tasks module
Route::resource('tasks', 'TaskController')->only(['index'])->middleware(['auth', 'role:Admin']);

// Notification Templates module
Route::resource('notification-templates', 'NotificationTemplateController')->only(['index'])->middleware(['auth', 'role:Admin']);

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
        echo Artisan::call('db:seed', ['--class' => UserTableSeeder::class]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Seed Singers
    // db:seed --class=SingerTableSeeder
    Route::get('singers', function(){
        echo Artisan::call('db:seed', [
            '--class' => SingerTableSeeder::class,
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // ONLY RUN ONCE
    // Seed Singer Categories
    // db:seed --class=SingerCategorySeeder
    Route::get('singer-categories', function(){
        echo Artisan::call('db:seed', [
            '--class' => SingerCategorySeeder::class,
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Seed Tasks
    // db:seed --class=TaskTableSeeder
    Route::get('tasks', function(){
        echo Artisan::call('db:seed', [
            '--class' => TaskTableSeeder::class,
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Seed Templates
    // db:seed --class=NotificationTemplateSeeder
    Route::get('templates', function(){
        echo Artisan::call('db:seed', [
            '--class' => NotificationTemplateSeeder::class,
            '--force' => true,
        ]);
        echo '<pre>'.Artisan::output().'</pre>';
    });

    // Singer import
    Route::get('import', [SingerController::class, 'import']);
});


// Public calendar feed
Route::get('/events-ical', [ICalController::class, 'index']);