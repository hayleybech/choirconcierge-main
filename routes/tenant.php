<?php

declare(strict_types=1);

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
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

    Auth::routes(['register' => false]);

    // Dashboard
    Route::get('/', [DashController::class, 'index'])->name('dash');

    // Singers module
    Route::resource('singers', 'SingerController')->middleware('auth');
    Route::resource('singers.profiles', 'SingerProfileController')->only(['create', 'store', 'edit', 'update'])->middleware('auth');
    Route::resource('singers.placements', 'SingerPlacementController')->only(['create', 'store', 'edit', 'update'])->middleware('auth');
    Route::post('singers/import', 'ImportSingerController')->name('singers.import');
    Route::get('singers/{singer}/category/update', 'UpdateSingerCategoryController')->name('singers.categories.update');
    Route::get('singers/{singer}/tasks/{task}/complete', 'CompleteSingerTaskController')->name('task.complete');

    // Songs module
    Route::resource('songs', 'SongController');
    Route::resource('songs.attachments', 'SongAttachmentController')->only(['store', 'show', 'destroy'])->middleware('employee');

    // Events module
    Route::resource('events', 'EventController');
    Route::resource('events.rsvps', 'RsvpController')->only(['store', 'update', 'destroy']);
    Route::resource('events.attendances', 'AttendanceController')->only(['index']);
    Route::post('events/{event}/attendances', [AttendanceController::class, 'updateAll'])->name('events.attendances.updateAll');
    Route::get('events/reports/attendance', 'AttendanceReportController')->name('events.reports.attendance');

    // Documents module
    Route::resource('folders', 'FolderController')->middleware('auth');
    Route::resource('folders.documents', 'DocumentController')->only(['store', 'show', 'destroy'])->middleware('auth');

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

    // Roles module
    Route::resource('roles', 'RoleController');

    // Public calendar feed
    Route::get('/events-ical', [ICalController::class, 'index']);
});
