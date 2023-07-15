<?php

declare(strict_types=1);

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AttendanceReportController;
use App\Http\Controllers\CompleteSingerTaskController;
use App\Http\Controllers\DashController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\EnsembleController;
use App\Http\Controllers\EventActivityController;
use App\Http\Controllers\EventCalendarController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\FindSongController;
use App\Http\Controllers\FolderController;
use App\Http\Controllers\ICalController;
use App\Http\Controllers\ImpersonateUserController;
use App\Http\Controllers\ImportSingerController;
use App\Http\Controllers\LearningStatusController;
use App\Http\Controllers\MailboxController;
use App\Http\Controllers\MoveActivityController;
use App\Http\Controllers\RecurringEventController;
use App\Http\Controllers\RiserStackController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\RsvpController;
use App\Http\Controllers\Search\FindSingerController;
use App\Http\Controllers\Search\GlobalFindUserController;
use App\Http\Controllers\BroadcastController;
use App\Http\Controllers\SingerController;
use App\Http\Controllers\TenantAssetsController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UpdateSingerFeeController;
use App\Http\Controllers\SingerPlacementController;
use App\Http\Controllers\SongAttachmentController;
use App\Http\Controllers\SongController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskNotificationTemplateController;
use App\Http\Controllers\UpdateMyLearningStatusController;
use App\Http\Controllers\UpdateSingerCategoryController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserGroupController;
use App\Http\Controllers\VoicePartController;
use App\Http\Middleware\EnsureUserIsMember;
use App\Http\Middleware\NoRobots;
use App\Http\Middleware\RedirectTenantFromDomainToFolder;
use App\Http\Middleware\SetTenantRouteParam;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain;
use Stancl\Tenancy\Middleware\InitializeTenancyByPath;

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



// Redirect subdomains to folders
// test.choirconcierge.com/singers => choirconcierge.com/test/singers
Route::middleware([
	InitializeTenancyByDomainOrSubdomain::class,
	RedirectTenantFromDomainToFolder::class
])->domain('{tenant}.'.config('tenancy.central_domains')[0])->group(function () {
	Route::any('/{any}', function ($any) {
		// any other url, subfolders also
	})->where('any', '.*');
});

Route::middleware([
    'web',
	InitializeTenancyByPath::class,
    SetTenantRouteParam::class,
    NoRobots::class,
])->prefix('/{tenant}')->group(function () {

    // Tenant asset - path version as path tenancy isn't supported by the library
    Route::get('/tenancy/assets/{path?}', [TenantAssetsController::class, 'asset'])
        ->where('path', '(.*)')
        ->name('tenancy.asset');

    // Public calendar feed
    Route::get('/events-ical', [ICalController::class, 'index'])->name('events.feed');

    /** Mailbox **/
    Route::get('/mailbox/process', [MailboxController::class, 'process']);

    Route::middleware([
        'auth',
        EnsureUserIsMember::class
    ])->group(function() {

        // Dashboard
        Route::get('/', [DashController::class, 'index'])->name('dash');

        // Account Settings
        Route::get('account/edit', [AccountController::class, 'edit'])->name('accounts.edit');
        Route::post('account', [AccountController::class, 'update'])->name('accounts.update');

        // Singers module
        Route::resource('singers', SingerController::class);
        Route::resource('singers.placements', SingerPlacementController::class)->only(['create', 'store', 'edit', 'update']);
        Route::put('singers/{singer}/fees', UpdateSingerFeeController::class)->name('singers.fees.update');
        Route::post('singers/import', ImportSingerController::class)->name('singers.import');
        Route::get('singers/{singer}/category/update', UpdateSingerCategoryController::class)->name('singers.categories.update');
        Route::get('singers/{singer}/tasks/{task}/complete', CompleteSingerTaskController::class)->name('task.complete');

        // Songs module
        Route::resource('songs', SongController::class);
        Route::resource('songs.attachments', SongAttachmentController::class)->only(['store', 'show', 'update', 'destroy'])->middleware('employee');
        Route::post('songs/{song}/my-learning', UpdateMyLearningStatusController::class)->name('songs.my-learning.update');
        Route::resource('songs.singers', LearningStatusController::class)->only(['index', 'update']);

        // Events module
        Route::resource('events', EventController::class);
        Route::resource('events.rsvps', RsvpController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::resource('events.attendances', AttendanceController::class)->only(['index']);
        Route::resource('events.activities', EventActivityController::class)->only(['store', 'update', 'destroy']);
        Route::post('events/{event}/activities/{activity}/move', MoveActivityController::class)->name('events.activities.move');
        Route::get('events/calendar/month', EventCalendarController::class)->name('events.calendar.month');
        Route::controller(RecurringEventController::class)
            ->prefix('events/{event}/recurring/')
            ->name('events.recurring.')
            ->group(function () {
                Route::get('edit/{mode}', 'edit')->name('edit');
                Route::put('{mode}', 'update')->name('update');
                Route::get('delete/{mode}', 'destroy')->name('delete');
            });
        Route::put('events/{event}/attendances/{singer}', [AttendanceController::class, 'update'])->name('events.attendances.update');
        Route::post('events/{event}/attendances', [AttendanceController::class, 'updateAll'])->name('events.attendances.updateAll');
        Route::get('events/reports/attendance', AttendanceReportController::class)->name('events.reports.attendance');

        // Documents module
        Route::resource('folders', FolderController::class)->except(['show', 'edit']);
        Route::resource('folders.documents', DocumentController::class)->only(['store', 'show', 'update', 'destroy']);

        // Risers module
        Route::resource('stacks', RiserStackController::class);

        // Users/Team module
        Route::prefix('users')->group(static function () {

            Route::controller(UserController::class)->group(function () {
                // Index
                Route::get('/', 'index')->name('users.index');

                // AJAX Search
                Route::get('/find', 'findUsers')->name('findUsers');
                Route::get('/roles/find', 'findRoles')->name('findRoles');
                Route::get('/voice-parts/find', 'findVoiceParts')->name('findVoiceParts');
                Route::get('/singer-categories/find', 'findSingerCategories')->name('findSingerCategories');

                // Attach/Detach role from a user
                Route::get('{user}/roles/{role}/detach', 'detachRole')->name('users.detachrole');
                Route::post('{user}/role', 'addRoles')->name('users.addroles');
            });


            // User Impersonation
            Route::get('{user}/impersonate', [ImpersonateUserController::class, 'start'])->name('users.impersonate');
            Route::get('/impersonation/stop', [ImpersonateUserController::class, 'stop'])->name('impersonation.stop');
        });

        // Search APIs
        Route::prefix('find')->name('find.')->group(function () {
            Route::get('/singers', FindSingerController::class)->name('singers');
            Route::get('/songs/{keyword}', FindSongController::class)->name('songs');
        });

        // Global Search APIs
        Route::prefix('find')->group(function () {
            Route::get('/users', GlobalFindUserController::class)->name('global-find.users');
        });

        // Mailing Lists (User Groups) module
        Route::get('/groups/broadcasts/create', [BroadcastController::class, 'create'])->name('groups.broadcasts.create');
        Route::post('/groups/broadcasts', [BroadcastController::class, 'store'])->name('groups.broadcasts.store');
        Route::resource('groups', UserGroupController::class);

        // Tasks module
        Route::resource('tasks', TaskController::class)->only(['index', 'create', 'store', 'show', 'destroy']);
        Route::resource('tasks.notifications', TaskNotificationTemplateController::class)->except('index');

        // Voice Parts module
        Route::resource('voice-parts', VoicePartController::class)->except(['show']);

        // Roles module
        Route::resource('roles', RoleController::class);

        // Organisation Settings
        Route::get('/organisation', [TenantController::class, 'edit'])->name('organisation.edit');
        Route::post('/organisation', [TenantController::class, 'update'])->name('organisation.update');

        // Sub-groups aka Ensembles aka Choirs
		Route::resource('organisations.ensembles', EnsembleController::class)->only(['store', 'update']);
    });
});
