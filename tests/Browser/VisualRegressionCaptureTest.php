<?php

use App\Models\Membership;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Poll;
use App\Models\Song;
use App\Models\SongCategory;
use App\Models\Task;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Support\VisualRegression\ScreenshotCatalog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Tests\Browser\VisualRegressionTestCase;

uses(VisualRegressionTestCase::class)->group('visual');

test('captures every catalog case at every viewport without JavaScript errors', function () {
    $catalog = app(ScreenshotCatalog::class);
    $latestDirectory = config('visual-regression.latest_directory');
    $baselineDirectory = config('visual-regression.baseline_directory');

    expect(app()->environment())
        ->toBe('testing')
        ->and(config('database.connections.mysql.database'))
        ->toMatch('/^choirconcierge_test(?:_[0-9]+(?:\.[0-9]+)?)?$/');

    config(['app.debug' => false]);
    Carbon::setTestNow('2026-10-23 12:00:00');
    $authenticatedUser = null;
    $visualRegressionTenant = null;
    $tenantAdminSetUp = false;

    try {
        foreach ($catalog->cases() as $case) {
            if (in_array($case['persona'], ['authenticated-user', 'tenant-admin'], true)) {
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

                    $membership = Membership::factory()->create([
                        'user_id' => $authenticatedUser->id,
                        'joined_at' => '2000-01-01 00:00:00',
                    ]);

                    $membership->roles()->attach($role->id);
                    $authenticatedUser->unsetRelation('membership');
                    expect($membership->tenant_id)->toBe($visualRegressionTenant->id);

                    $eventType = EventType::create(['title' => 'Rehearsal']);
                    Event::factory()->create([
                        'id' => 1,
                        'title' => 'Autumn Rehearsal',
                        'type_id' => $eventType->id,
                        'call_time' => Carbon::parse('2027-01-05 17:00:00'),
                        'start_date' => Carbon::parse('2027-01-05 18:00:00'),
                        'end_date' => Carbon::parse('2027-01-05 20:00:00'),
                        'location_name' => 'Community Hall',
                        'location_address' => '1 Test Street',
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
                    SongCategory::create(['title' => 'Warm-ups']);

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

            foreach ($catalog->viewports() as $viewport) {
                File::ensureDirectoryExists($latestDirectory . DIRECTORY_SEPARATOR . $case['id']);
                File::ensureDirectoryExists($baselineDirectory . DIRECTORY_SEPARATOR . $case['id']);

                $page = visit(route($case['route'], $case['parameters'], false))->resize(
                    $viewport['width'],
                    $viewport['height'],
                );

                expect($page->script("JSON.parse(document.querySelector('#app').dataset.page).component"))->toBe(
                    $case['component'],
                );

                $page->wait(0.25)->assertNoJavaScriptErrors();
                $page->assertSee($case['assert_text']);
                $page->script(
                    "document.head.insertAdjacentHTML('beforeend', '<style>*{animation:none!important;transition:none!important;font-family:Arial,sans-serif!important;}</style>')",
                );
                $page->screenshot(true, 'Latest/' . $case['id'] . '/' . $viewport['id'] . '.png');
            }
        }
    } finally {
        Carbon::setTestNow();
    }
});
