<?php

declare(strict_types=1);

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\CompleteSingerTaskController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ICalController;
use App\Http\Controllers\ImpersonateUserController;
use App\Http\Controllers\ImportSingerController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\RiserStackController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\SingerController;
use App\Http\Controllers\SingerPlacementController;
use App\Http\Controllers\SingerProfileController;
use App\Http\Controllers\SongAttachmentController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskNotificationTemplateController;
use App\Http\Controllers\UpdateSingerCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\VoicePartController;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

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
    Route::resource('singers', SingerController::class)->middleware('auth');
    Route::resource('singers.profiles', SingerProfileController::class)->only(['create', 'store', 'edit', 'update'])->middleware('auth');
    Route::resource('singers.placements', SingerPlacementController::class)->only(['create', 'store', 'edit', 'update'])->middleware('auth');
    Route::post('singers/import', ImportSingerController::class)->name('singers.import');
    Route::get('singers/{singer}/category/update', UpdateSingerCategoryController::class)->name('singers.categories.update');
    Route::get('singers/{singer}/tasks/{task}/complete', CompleteSingerTaskController::class)->name('task.complete');


    // Songs module
    Route::resource('songs', SongController::class);
    Route::resource('songs.attachments', SongAttachmentController::class)->only(['store', 'show', 'destroy'])->middleware('employee');

    // Events module
    Route::resource('events', EventController::class);
    Route::resource('events.rsvps', RsvpController::class)->only(['store', 'update', 'destroy']);
    Route::resource('events.attendances', AttendanceController::class)->only(['index']);
    Route::post('events/{event}/attendances', [AttendanceController::class, 'updateAll'])->name('events.attendances.updateAll');
    Route::get('events/reports/attendance', AttendanceReportController::class)->name('events.reports.attendance');

    // Documents module
    Route::resource('folders', FolderController::class)->middleware('auth');
    Route::resource('folders.documents', DocumentController::class)->only(['store', 'show', 'destroy'])->middleware('auth');

    // Risers module
    Route::resource('stacks', RiserStackController::class);

    // Users/Team module
    Route::prefix('users')->middleware(['auth'])->group(static function () {
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

        // User Impersonation
        Route::get('{user}/impersonate', [ImpersonateUserController::class, 'start'])->name('users.impersonate');
        Route::get('/impersonation/stop', [ImpersonateUserController::class, 'stop'])->name('impersonation.stop');
    });

    // Mailing Lists (User Groups) module
    Route::resource('groups', UserGroupController::class)->middleware(['auth']);

    /** Mailbox **/
    Route::get('/mailbox/process', [MailboxController::class, 'process']);

    // Tasks module
    Route::resource('tasks', TaskController::class)->only(['index', 'create', 'store', 'show', 'destroy'])->middleware(['auth', 'role:Admin']);
    Route::resource('tasks.notifications', TaskNotificationTemplateController::class)->except('index');

    // Voice Parts module
    Route::resource('voice-parts', VoicePartController::class);

    // Roles module
    Route::resource('roles', RoleController::class);

    // Public calendar feed
    Route::get('/events-ical', [ICalController::class, 'index']);
});
