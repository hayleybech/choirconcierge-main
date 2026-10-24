<?php

use App\Models\Membership;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Folder;
use App\Models\MailLog;
use App\Models\MailLogEvent;
use App\Models\NotificationTemplate;
use App\Models\Poll;
use App\Models\Placement;
use App\Models\RiserStack;
use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\SongCategory;
use App\Models\Task;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\VoicePart;
use App\Support\VisualRegression\ScreenshotCatalog;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\Browser\VisualRegressionTestCase;

uses(VisualRegressionTestCase::class)->group('visual');

const VISUAL_REGRESSION_SCREENSHOT_STABILIZATION_CSS = '*:not(.fa):not(.fas):not(.far):not(.fal):not(.fad):not(.fab){animation:none!important;transition:none!important;font-family:Arial,sans-serif!important;}';

test('captures every catalog case at every viewport without JavaScript errors', function () {
    $catalog = app(ScreenshotCatalog::class);
    $latestDirectory = config('visual-regression.latest_directory');
    $baselineDirectory = config('visual-regression.baseline_directory');

    $testDatabase = config('database.connections.mysql.database');
    expect(app()->environment())
        ->toBe('testing')
        ->and($testDatabase)
        ->toBeString()
        ->not->toBeEmpty();

    $originalSuperAdminEmail = config('app.super_admin_email', 'hayleybech@gmail.com');
    config(['app.debug' => false]);
    config(['app.super_admin_email' => 'visual-regression-admin@example.test']);
    Carbon::setTestNow('2026-10-23 12:00:00');
    $authenticatedUser = null;
    $tenantSuperAdmin = null;
    $visualRegressionTenant = null;
    $visualRegressionEventId = null;
    $visualRegressionTodayEventId = null;
    $visualRegressionMembershipId = null;
    $visualRegressionAdminRoleId = null;
    $visualRegressionMailLogId = null;
    $visualRegressionMailingListId = null;
    $visualRegressionNotificationId = null;
    $visualRegressionPlacementId = null;
    $visualRegressionFolderId = null;
    $visualRegressionRiserStackId = null;
    $visualRegressionVoicePartId = null;
    $tenantAdminSetUp = false;
    $browserOrigin = null;
    $screenshotStyle = json_encode(
        '<style>' . VISUAL_REGRESSION_SCREENSHOT_STABILIZATION_CSS . '</style>',
        JSON_THROW_ON_ERROR,
    );
    $googlePlacesMock = <<<'JS'
window.google = {
    maps: {
        places: {
            AutocompleteSessionToken: class {},
            AutocompleteService: class {
                getPlacePredictions(request, callback) {
                    callback([], 'ZERO_RESULTS');
                }
            },
            PlacesService: class {
                getDetails(request, callback) {
                    callback(null, 'ZERO_RESULTS');
                }
            },
            PlacesServiceStatus: {
                OK: 'OK',
                ZERO_RESULTS: 'ZERO_RESULTS',
            },
        },
    },
};
JS;
    $cases = collect($catalog->cases());
    $caseIdFilter = getenv('VISUAL_REGRESSION_CASE_ID');

    if (is_string($caseIdFilter) && $caseIdFilter !== '') {
        $cases = $cases->where('id', $caseIdFilter)->values();

        if ($cases->isEmpty()) {
            throw new \InvalidArgumentException('Unknown visual-regression case ID: ' . $caseIdFilter);
        }
    }

    try {
        foreach ($cases as $case) {
            if (in_array($case['persona'], ['authenticated-user', 'tenant-admin', 'tenant-empty-admin', 'tenant-super-admin'], true)) {
                if ($authenticatedUser === null) {
                    $authenticatedUser = User::factory()->create([
                        'first_name' => 'Visual',
                        'last_name' => 'Regression',
                        'email' => 'visual-regression@example.test',
                        'dob' => '1990-01-15',
                        'phone' => '555-0100',
                        'ice_name' => 'Emergency Contact',
                        'ice_phone' => '555-0101',
                        'address_street_1' => '1 Test Street',
                        'address_street_2' => 'Suite 2',
                        'address_suburb' => 'Testville',
                        'address_state' => 'WA',
                        'address_postcode' => '6000',
                        'profession' => 'Synthetic Test Profile',
                        'skills' => 'Choir singing',
                        'height' => 170,
                        'bha_id' => '1234',
                    ]);
                }

                $this->actingAs($authenticatedUser);
            }

            if (
                in_array($case['id'], ['central-tenant-show', 'central-tenant-onboarding'], true) ||
                $case['persona'] === 'tenant-admin'
            ) {
                $visualRegressionTenant ??= Tenant::factory()
                    ->withDomain()
                    ->create([
                        'id' => 'visual-regression',
                        'name' => 'Visual Regression Choir',
                        'timezone' => 'Australia/Perth',
                        'created_by' => $authenticatedUser->id,
                        'data' => ['has_gratis' => true],
                    ]);
            }

            if ($case['persona'] === 'tenant-admin' && !$tenantAdminSetUp) {
                tenancy()->initialize($visualRegressionTenant);

                try {
                    $role = Role::create([
                        'name' => 'Admin',
                        'abilities' => Role::ALL_ABILITIES,
                    ]);
                    $visualRegressionAdminRoleId = $role->id;

                    $membership = Membership::factory()->create([
                        'user_id' => $authenticatedUser->id,
                        'joined_at' => '2000-01-01 00:00:00',
                        'reason_for_joining' => 'Synthetic visual-regression membership.',
                        'referrer' => 'Visual regression fixture',
                        'membership_details' => 'Created only for deterministic browser captures.',
                    ]);
                    $visualRegressionMembershipId = $membership->id;

                    $membership->roles()->attach($role->id);
                    $authenticatedUser->unsetRelation('membership');
                    expect($membership->tenant_id)->toBe($visualRegressionTenant->id);

                    $eventType = EventType::create(['title' => 'Rehearsal']);
                    $event = Event::factory()->create([
                        'id' => 1,
                        'title' => 'Autumn Rehearsal',
                        'type_id' => $eventType->id,
                        'call_time' => Carbon::parse('2027-01-05 17:00:00'),
                        'start_date' => Carbon::parse('2027-01-05 18:00:00'),
                        'end_date' => Carbon::parse('2027-01-05 20:00:00'),
                        'location_name' => 'Community Hall',
                        'location_address' => '1 Test Street',
                    ]);
                    $visualRegressionEventId = $event->id;

                    $checkInEvent = Event::factory()->create([
                        'title' => 'Open Rehearsal',
                        'type_id' => $eventType->id,
                        'call_time' => now()->subHour(),
                        'start_date' => now()->subMinutes(30),
                        'end_date' => now()->addHour(),
                    ]);
                    $visualRegressionTodayEventId = $checkInEvent->id;

                    $performanceType = EventType::create(['title' => 'Performance']);
                    $performanceStart = now()->subMonths(2)->setTime(18, 0);
                    $performance = Event::factory()->create([
                        'title' => 'Winter Concert',
                        'type_id' => $performanceType->id,
                        'call_time' => $performanceStart->copy()->subHour(),
                        'start_date' => $performanceStart,
                        'end_date' => $performanceStart->copy()->addHours(2),
                    ]);
                    Attendance::factory()->create([
                        'membership_id' => $membership->id,
                        'event_id' => $performance->id,
                        'response' => 'present',
                        'source' => 'qr-code',
                    ]);

                    $now = now();
                    DB::table('song_statuses')->insert([
                        ['tenant_id' => tenant('id'), 'title' => 'Pending', 'created_at' => $now, 'updated_at' => $now],
                        ['tenant_id' => tenant('id'), 'title' => 'Learning', 'created_at' => $now, 'updated_at' => $now],
                        ['tenant_id' => tenant('id'), 'title' => 'Active', 'created_at' => $now, 'updated_at' => $now],
                        ['tenant_id' => tenant('id'), 'title' => 'Archived', 'created_at' => $now, 'updated_at' => $now],
                    ]);
                    Song::factory()->create([
                        'id' => 1,
                        'title' => 'Northern Lights',
                        'status_id' => DB::table('song_statuses')->where('title', 'Active')->value('id'),
                        'pitch_blown' => 0,
                    ]);
                    SongAttachment::create([
                        'song_id' => 1,
                        'title' => 'Sample Sheet Music',
                        'file' => new UploadedFile(
                            base_path('storage/app/sample/pdf/pdf-sample.pdf'),
                            'pdf-sample.pdf',
                            'application/pdf',
                            UPLOAD_ERR_OK,
                            true,
                        ),
                        'filepath' => 'pdf-sample.pdf',
                        'type' => 'sheet-music',
                    ]);
                    SongCategory::create(['title' => 'Warm-ups']);

                    $folder = Folder::factory()->create(['title' => 'Visual Regression Documents']);
                    $visualRegressionFolderId = $folder->id;
                    $mailingList = UserGroup::factory()->create([
                        'title' => 'Visual Regression Mailing List',
                        'slug' => 'visual-regression-mailing-list',
                        'tenant_id' => tenant('id'),
                        'list_type' => 'distribution',
                    ]);
                    $visualRegressionMailingListId = $mailingList->id;

                    $mailLog = MailLog::factory()->create([
                        'uid' => 'broadcast-visual-regression',
                        'from' => 'choir@example.test',
                        'to' => 'member@example.test',
                        'cc' => null,
                        'bcc' => null,
                        'subject' => 'Visual Regression Broadcast',
                        'body' => 'A deterministic broadcast for screenshot coverage.',
                        'size' => 512,
                        'has_attachments' => false,
                        'received_at' => now()->subMinutes(10),
                        'created_at' => now()->subMinutes(10),
                        'updated_at' => now()->subMinutes(10),
                    ]);
                    $mailLog->tenants()->attach($visualRegressionTenant->id);
                    $visualRegressionMailLogId = $mailLog->id;
                    MailLogEvent::create([
                        'mail_log_id' => $mailLog->id,
                        'user_group_id' => $mailingList->id,
                        'status' => 'clones-sent',
                        'context' => null,
                        'created_at' => now()->subMinutes(9),
                        'updated_at' => now()->subMinutes(9),
                    ]);

                    $voicePart = VoicePart::factory()->create([
                        'title' => 'Visual Regression Voice Part',
                        'colour' => '#336699',
                    ]);
                    $visualRegressionVoicePartId = $voicePart->id;

                    $placement = Placement::factory()->create([
                        'membership_id' => $membership->id,
                        'experience' => 'Synthetic choir experience',
                        'instruments' => 'Piano',
                        'skill_pitch' => 3,
                        'skill_harmony' => 3,
                        'skill_performance' => 3,
                        'skill_sightreading' => 3,
                        'voice_tone' => 3,
                    ]);
                    $visualRegressionPlacementId = $placement->id;

                    $notification = NotificationTemplate::factory()->create([
                        'task_id' => 1,
                        'subject' => 'Visual Regression Task Notification',
                        'recipients' => 'role:' . $role->id,
                        'body' => 'A deterministic task notification body.',
                        'delay' => '1 day',
                    ]);
                    $visualRegressionNotificationId = $notification->id;

                    $riserStack = RiserStack::factory()->create(['title' => 'Visual Regression Stack']);
                    $visualRegressionRiserStackId = $riserStack->id;

                    Task::factory()->create([
                        'id' => 1,
                        'name' => 'Pass Audition',
                        'role_id' => $role->id,
                    ]);

                    $poll = Poll::factory()->create([
                        'id' => 1,
                        'title' => 'Rehearsal Preference',
                        'description' => 'Choose a rehearsal time.',
                    ]);
                    $poll->options()->create(['label' => 'Tuesday evening']);
                } finally {
                    tenancy()->end();
                }

                $this->actingAs($authenticatedUser);
                $tenantAdminSetUp = true;
            }

            if ($case['state'] === 'empty' && isset($case['parameters']['tenant'])) {
                if (tenancy()->initialized) {
                    tenancy()->end();
                }

                $emptyTenant = Tenant::withoutEvents(fn () => Tenant::factory()
                    ->withDomain()
                    ->create([
                        'id' => $case['parameters']['tenant'],
                        'name' => 'Visual Regression Empty State',
                        'timezone' => 'Australia/Perth',
                        'created_by' => $authenticatedUser->id,
                        'data' => ['has_gratis' => true],
                    ]));

                tenancy()->initialize($emptyTenant);

                try {
                    if ($case['persona'] === 'tenant-empty-admin') {
                        $role = Role::create([
                            'name' => 'Admin',
                            'abilities' => Role::ALL_ABILITIES,
                        ]);
                        $membership = Membership::factory()->create([
                            'user_id' => $authenticatedUser->id,
                            'joined_at' => '2000-01-01 00:00:00',
                        ]);
                        $membership->roles()->attach($role->id);
                        $authenticatedUser->unsetRelation('membership');
                    }

                    if (in_array($case['route'], ['events.rsvps.index', 'events.attendances.index'], true)) {
                        $eventType = EventType::create(['title' => 'Rehearsal']);
                        $event = Event::factory()->create([
                            'type_id' => $eventType->id,
                            'title' => 'Empty State Rehearsal',
                            'call_time' => Carbon::parse('2027-01-05 17:00:00'),
                            'start_date' => Carbon::parse('2027-01-05 18:00:00'),
                            'end_date' => Carbon::parse('2027-01-05 20:00:00'),
                        ]);
                        $case['parameters']['event'] = $event->id;
                    }

                    if ($case['component'] === 'Songs/Show') {
                        $now = now();
                        $statusId = DB::table('song_statuses')->insertGetId([
                            'tenant_id' => tenant('id'),
                            'title' => 'Active',
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                        $song = Song::factory()->create([
                            'title' => 'Northern Lights',
                            'status_id' => $statusId,
                            'pitch_blown' => 0,
                        ]);
                        $case['parameters']['song'] = $song->id;
                    }
                } finally {
                    tenancy()->end();
                }
            }

            if (in_array($case['persona'], ['tenant-super-admin', 'central-admin'], true)) {
                $tenantSuperAdmin ??= User::factory()->create([
                    'first_name' => 'Visual',
                    'last_name' => 'Regression',
                    'email' => config('app.super_admin_email'),
                ]);

                $this->actingAs($tenantSuperAdmin);
            }

            if (isset($case['parameters']['tenant'])) {
                if (tenancy()->initialized) {
                    tenancy()->end();
                }

                if (tenancy()->find($case['parameters']['tenant']) === null) {
                    throw new \RuntimeException(
                        'Visual screenshot case [' . $case['id'] . '] references an unknown tenant: '
                        . $case['parameters']['tenant'],
                    );
                }
            }

            foreach ($catalog->viewports() as $viewport) {
                File::ensureDirectoryExists($latestDirectory . DIRECTORY_SEPARATOR . $case['id']);
                File::ensureDirectoryExists($baselineDirectory . DIRECTORY_SEPARATOR . $case['id']);

                if ($case['component'] === 'Roles/Show') {
                    $case['parameters']['role'] = $visualRegressionAdminRoleId;
                }

                if ($case['component'] === 'RiserStacks/Show') {
                    $case['parameters']['stack'] = $visualRegressionRiserStackId;
                }

                if ($case['component'] === 'RiserStacks/Edit') {
                    $case['parameters']['stack'] = $visualRegressionRiserStackId;
                }

                if ($case['component'] === 'Folders/Edit') {
                    $case['parameters']['folder'] = $visualRegressionFolderId;
                }

                if ($case['component'] === 'Roles/Edit') {
                    $case['parameters']['role'] = $visualRegressionAdminRoleId;
                }

                if ($case['state'] !== 'empty'
                    && in_array($case['component'], ['Events/Show', 'Events/Edit', 'Events/Rsvps/Index', 'Events/Attendance/Index'], true)) {
                    $case['parameters']['event'] = $visualRegressionEventId;
                }

                if (in_array($case['component'], ['Events/Attendance/CheckIn', 'Events/Attendance/CheckInKiosk'], true)) {
                    $case['parameters']['event'] = $visualRegressionTodayEventId;
                }

                if (in_array($case['component'], [
                    'Singers/Attendance/Index',
                    'Singers/Edit',
                    'Singers/Placements/Create',
                    'Singers/Placements/Edit',
                    'Singers/Show',
                ], true)) {
                    $case['parameters']['singer'] = $visualRegressionMembershipId;
                }

                if ($case['component'] === 'Singers/Placements/Edit') {
                    $case['parameters']['placement'] = $visualRegressionPlacementId;
                }

                if (in_array($case['component'], ['Central/MailLogs/Show', 'MailingLists/MailLogs/Show'], true)) {
                    $case['parameters']['mail_log'] = $visualRegressionMailLogId;
                }

                if (in_array($case['component'], ['MailingLists/Edit', 'MailingLists/Show'], true)) {
                    $case['parameters']['group'] = $visualRegressionMailingListId;
                }

                if (in_array($case['component'], ['Tasks/Notifications/Edit', 'Tasks/Notifications/Show'], true)) {
                    $case['parameters']['notification'] = $visualRegressionNotificationId;
                }

                if ($case['component'] === 'VoiceParts/Edit') {
                    $case['parameters']['voice_part'] = $visualRegressionVoicePartId;
                }

                $pageUrl = route($case['route'], $case['parameters'], false);

                if ($case['component'] === 'Events/Attendance/CheckIn') {
                    if ($browserOrigin === null) {
                        $bootstrapPage = visit(route('login', [], false));
                        $browserOrigin = $bootstrapPage->script('window.location.origin');
                    }

                    if (! is_string($browserOrigin) || $browserOrigin === '') {
                        throw new \RuntimeException('Unable to determine the browser server origin for the signed check-in capture.');
                    }

                    $originalRootUrl = URL::to('/');

                    try {
                        URL::forceRootUrl($browserOrigin);
                        $pageUrl = URL::temporarySignedRoute(
                            $case['route'],
                            now()->addMinutes(5),
                            $case['parameters'],
                        );
                    } finally {
                        URL::forceRootUrl($originalRootUrl);
                    }
                }

                if (in_array($case['component'], ['Events/Create', 'Events/Edit'], true)) {
                    $page = visit(route('login', [], false));
                    $browserOrigin ??= $page->script('window.location.origin');
                    $page->page()->context()->addInitScript($googlePlacesMock);
                    $page->page()->goto(rtrim($browserOrigin, '/') . $pageUrl);
                } elseif ($case['component'] === 'Singers/Placements/Edit') {
                    $page = visit(route('login', [], false));
                    $browserOrigin ??= $page->script('window.location.origin');
                    $page->page()->goto(rtrim($browserOrigin, '/') . $pageUrl);
                } else {
                    $page = visit($pageUrl);
                    $browserOrigin ??= $page->script('window.location.origin');
                }

                $page->resize($viewport['width'], $viewport['height']);

                $pageData = $page->script("JSON.parse(document.querySelector('#app').dataset.page)");
                $actualComponent = $pageData['component'] ?? null;

                if ($actualComponent !== $case['component']) {
                    throw new \RuntimeException(sprintf(
                        'Visual capture [%s] at [%s] expected Inertia component [%s], got [%s] with HTTP status [%s].',
                        $case['id'],
                        $pageUrl,
                        $case['component'],
                        $actualComponent ?? 'missing',
                        $pageData['props']['status'] ?? 'unknown',
                    ));
                }

                $page->wait(0.25)->assertNoJavaScriptErrors();
                $page->assertSee($case['assert_text']);

                if ($case['component'] === 'Songs/Show'
                    && $case['state'] === 'normal'
                    && in_array($viewport['id'], ['tablet', 'mobile'], true)) {
                    $page->assertSee('Attachments');
                }

                if (! empty($case['empty_props'])) {
                    $emptyProps = json_encode($case['empty_props'], JSON_THROW_ON_ERROR);
                    $emptyItemCounts = $page->script(
                        "(() => { const props = JSON.parse(document.querySelector('#app').dataset.page).props; const paths = {$emptyProps}; return paths.map(path => { const value = path.split('.').reduce((current, key) => current?.[key], props); if (Array.isArray(value)) return value.length; return value && typeof value === 'object' ? Object.keys(value).length : null; }); })()",
                    );

                    expect($emptyItemCounts)->toBe(array_fill(0, count($case['empty_props']), 0));
                }

                if ($case['pdf_viewer'] ?? false) {
                    $page->assertSee('Sample Sheet Music');
                    $page->click('Sample Sheet Music');

                    $pdfLoaded = $page->script(
                        "(async () => { const deadline = Date.now() + 10000; while (!document.querySelector('.react-pdf__Page canvas') && Date.now() < deadline) { await new Promise(resolve => setTimeout(resolve, 50)); } return document.querySelectorAll('.react-pdf__Page canvas').length > 0; })()",
                    );

                    $page->assertNoJavaScriptErrors();
                    expect($pdfLoaded)->toBeTrue();
                }

                $page->script(
                    "(async () => { await document.fonts.ready; document.head.insertAdjacentHTML('beforeend', {$screenshotStyle}); })()",
                );
                $page->screenshot(true, 'Latest/' . $case['id'] . '/' . $viewport['id'] . '.png');
            }
        }
    } finally {
        try {
            if (tenancy()->initialized) {
                tenancy()->end();
            }

            if ($visualRegressionTenant !== null) {
                tenancy()->initialize($visualRegressionTenant);
                Storage::disk('tenant')->delete('songs/1/pdf-sample.pdf');
            }
        } finally {
            if (tenancy()->initialized) {
                tenancy()->end();
            }

            Carbon::setTestNow();
            config(['app.super_admin_email' => $originalSuperAdminEmail]);
        }
    }
});

test('screenshot stabilization preserves loaded icon fonts', function () {
    $page = visit(route('login', [], false));
    $fontDiagnostics = $page->script(<<<'JS'
(async () => {
    const icon = document.createElement('i');
    icon.className = 'fas fa-user';
    icon.id = 'visual-regression-icon-font-test';
    document.body.append(icon);
    const loadedFaces = await document.fonts.load('900 16px "Font Awesome 5 Pro"');
    await document.fonts.ready;

    return {
        fontLoaded: loadedFaces.some(face => face.status === 'loaded'),
        fontFamilyBefore: getComputedStyle(icon).fontFamily,
    };
})()
JS);
    $screenshotStyle = json_encode(
        '<style>' . VISUAL_REGRESSION_SCREENSHOT_STABILIZATION_CSS . '</style>',
        JSON_THROW_ON_ERROR,
    );
    $page->script("document.head.insertAdjacentHTML('beforeend', {$screenshotStyle})");
    $fontFamilyAfter = $page->script(
        "getComputedStyle(document.querySelector('#visual-regression-icon-font-test')).fontFamily",
    );
    $page->script("document.querySelector('#visual-regression-icon-font-test').remove()");

    expect($fontDiagnostics['fontLoaded'])->toBeTrue()
        ->and($fontDiagnostics['fontFamilyBefore'])->toContain('Font Awesome 5 Pro')
        ->and($fontFamilyAfter)->toContain('Font Awesome 5 Pro');
});
