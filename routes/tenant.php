<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

use App\Http\Controllers\DashController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\ICalController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\SingerPlacementController;
use App\Http\Controllers\SingerProfileController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomainOrSubdomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'This is your multi-tenant application. The id of the current tenant is ' . tenant('id');
    });

    Auth::routes(['register' => false]);

    // Dashboard
    Route::get('/dash', [DashController::class, 'index'])->name('dash');

    // Singers module
    Route::prefix('singers')->middleware('auth')->group(static function (){

        // Singers - Any Employee
        Route::middleware('employee')->group(static function() {

            // Complete Task
            Route::get('{singer}/tasks/{task}/complete', 'CompleteSingerTaskController')->name('task.complete');
        });

        // Singers - Membership Team
        Route::middleware('role:Membership Team')->group(static function() {

            // Create Profile
            Route::get('{singer}/profile/create', [SingerProfileController::class, 'create'])->name('profile.create');
            Route::post('{singer}/profile', [SingerProfileController::class, 'store'])->name('profile');
            Route::get('{singer}/profile/{profile}/edit', [SingerProfileController::class, 'edit'])->name('profiles.edit');
            Route::put('{singer}/profile/{profile}', [SingerProfileController::class, 'update'])->name('profiles.update');

            // Move Singer
            Route::get('{singer}/category/update', 'UpdateSingerCategoryController')->name('singers.categories.update');
        });

        // Singers - Music Team
        Route::middleware('role:Music Team')->group(static function() {
            // Create Voice Placement
            Route::get('{singer}/placement/create', [SingerPlacementController::class, 'create'])->name('placement.create');
            Route::post('{singer}/placement', [SingerPlacementController::class, 'store'])->name('placement');
            Route::get('{singer}/placement/{placement}/edit', [SingerPlacementController::class, 'edit'])->name('placements.edit');
            Route::put('{singer}/placement/{placement}', [SingerPlacementController::class, 'update'])->name('placements.update');
        });
    });
    Route::resource('singers', 'SingerController')->middleware('auth');


    // Songs module
    Route::resource('songs', 'SongController');
    Route::resource('songs.attachments', 'SongAttachmentController')->only(['store', 'show', 'destroy'])->middleware('employee');

    // Events module
    Route::resource('events', 'EventController');

    // Documents module
    Route::prefix('folders')->group(static function (){

        Route::middleware(['auth', 'employee'])->group(static function() {
            // Create/Delete documents
            Route::post('{folder}/documents', [DocumentController::class, 'store'])->name('folders.documents.store');
            Route::get('{folder}/documents/{document}/delete', [DocumentController::class, 'destroy'])->name('folders.documents.delete');
        });
    });
    Route::resource('folders', 'FolderController')->middleware('auth');

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
    Route::resource('groups', 'UserGroupController')->middleware(['auth']);

    /** Mailbox **/
    Route::get('/mailbox/process', [MailboxController::class, 'process']);

    // Tasks module
    Route::resource('tasks', 'TaskController')->only(['index', 'create', 'store', 'show', 'destroy'])->middleware(['auth', 'role:Admin']);
    Route::resource('tasks.notifications', 'TaskNotificationTemplateController')->except('index');

    // Voice Parts module
    Route::resource('voice-parts', 'VoicePartController');

    // Public calendar feed
    Route::get('/events-ical', [ICalController::class, 'index']);
});
