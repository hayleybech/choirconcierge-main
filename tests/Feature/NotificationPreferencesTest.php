<?php

use App\Enums\EmailCategory;
use App\Models\Membership;
use App\Models\Event;
use App\Models\Poll;
use App\Models\Role;
use App\Models\Song;
use App\Models\User;
use App\Notifications\EventCreated;
use App\Notifications\PollCreated;
use App\Notifications\SongUploaded;
use App\Services\EmailPreferenceLinks;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

test('account settings render the account email preference defaults', function () {
    $user = User::factory()->has(Membership::factory())->create();
    $this->actingAs($user);

    $this->get(the_tenant_route('account.edit'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('email_preferences', [
                'communications' => true,
                'songs' => true,
                'events' => true,
                'documents' => true,
                'polls' => true,
                'attendance_reports' => true,
                'system' => true,
                'product' => true,
                'promotions' => false,
                'community' => false,
            ])
            ->where('can_receive_attendance_reports', false));
});

test('attendance reporting preferences are shown only to users who can view attendance', function (): void {
    $user = User::factory()->create();
    $membership = Membership::factory()->for($user)->create();
    $membership->roles()->attach(Role::factory()->create([
        'abilities' => ['attendances_view'],
    ]));
    $this->actingAs($user);

    $this->get(the_tenant_route('account.edit'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->where('can_receive_attendance_reports', true)
            ->where('email_preferences.attendance_reports', true));
});

test('users can save account-wide email preferences', function () {
    $user = User::factory()->has(Membership::factory())->create();
    $this->actingAs($user);
    $preferences = EmailCategory::defaults();
    $preferences['songs'] = false;
    $preferences['events'] = false;
    $preferences['product'] = false;
    $preferences['promotions'] = true;
    unset($preferences['attendance_reports']);

    $this->put(the_tenant_route('account.notifications.update'), [
        'email_preferences' => $preferences,
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->emailPreferences())
        ->toMatchArray($preferences)
        ->toHaveKey('attendance_reports', true);
});

test('eligible users can save the attendance reporting preference', function (): void {
    $user = User::factory()->create();
    $membership = Membership::factory()->for($user)->create();
    $membership->roles()->attach(Role::factory()->create([
        'abilities' => ['attendances_view'],
    ]));
    $this->actingAs($user);
    $preferences = EmailCategory::defaults();
    $preferences['attendance_reports'] = false;

    $this->put(the_tenant_route('account.notifications.update'), [
        'email_preferences' => $preferences,
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->emailPreferences()['attendance_reports'])->toBeFalse();
});

test('users without attendance report permission cannot change that hidden preference', function (): void {
    $user = User::factory()->create();
    expect($user->canReceiveAttendanceReports())->toBeFalse();
    $this->actingAs($user);
    $preferences = EmailCategory::defaults();
    $preferences['attendance_reports'] = false;

    $this->put(the_tenant_route('account.notifications.update'), [
        'email_preferences' => $preferences,
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->emailPreferences()['attendance_reports'])->toBeTrue();
});

test('mandatory email categories cannot be disabled', function () {
    $user = User::factory()->has(Membership::factory())->create();
    $this->actingAs($user);
    $preferences = EmailCategory::defaults();
    $preferences['communications'] = false;
    $preferences['system'] = false;
    unset($preferences['attendance_reports']);

    $this->put(the_tenant_route('account.notifications.update'), [
        'email_preferences' => $preferences,
    ])->assertSessionHasErrors([
        'email_preferences.communications',
        'email_preferences.system',
    ]);
});

test('signed unsubscribe links disable only the selected category without login', function () {
    $user = User::factory()->create();
    $user->updateEmailPreferences(['product' => false, 'promotions' => true]);
    $url = URL::signedRoute('email-preferences.unsubscribe', [
        'user' => $user->getKey(),
        'category' => 'promotions',
    ]);

    $this->get($url)->assertRedirect(route('login'));

    expect($user->fresh()->emailPreferences())
        ->toMatchArray(['product' => false, 'promotions' => false]);
});

test('mandatory categories cannot be unsubscribed through a signed link', function () {
    $user = User::factory()->create();
    $url = URL::signedRoute('email-preferences.unsubscribe', [
        'user' => $user->getKey(),
        'category' => 'system',
    ]);

    $this->get($url)->assertNotFound();
});

test('song emails respect the saved preference while event emails remain enabled', function () {
    $user = User::factory()->create();
    $user->updateEmailPreferences(['songs' => false]);

    expect((new SongUploaded(new Song()))->via($user))->toBe([])
        ->and((new EventCreated(new Event()))->via($user))->toBe(['mail']);
});

test('poll emails respect the saved preference', function () {
    $user = User::factory()->create();
    $notification = new PollCreated(Poll::factory()->create());

    expect($notification->via($user))->toBe(['mail']);

    $user->updateEmailPreferences(['polls' => false]);

    expect($notification->via($user))->toBe([]);
});

test('poll notification emails include the category preference footer', function () {
    $user = User::factory()->create();
    $poll = Poll::factory()->create();
    $rendered = (new PollCreated($poll))->toMail($user)->render();

    expect(str_contains($rendered, 'Poll Created/Updated'))->toBeTrue()
        ->and(str_contains($rendered, 'Unsubscribe from Poll Created/Updated'))->toBeTrue();
});

test('choir email preference footers identify the choir context', function (): void {
    $user = User::factory()->create();
    $emailPreferenceLinks = app(EmailPreferenceLinks::class)->make($user, EmailCategory::SONGS);
    $footer = view('emails.partials.email-preference-footer', compact('emailPreferenceLinks'))->render();

    expect($footer)->toContain('Song Created/Updated')
        ->toContain('emails for your choir.')
        ->toContain('font-size: 11px');
});

test('attendance report preference footer identifies its choir category', function (): void {
    $user = User::factory()->create();
    $emailPreferenceLinks = app(EmailPreferenceLinks::class)->make($user, EmailCategory::ATTENDANCE_REPORTS);
    $footer = view('emails.partials.email-preference-footer', compact('emailPreferenceLinks'))->render();

    expect($footer)
        ->toContain('Attendance Reporting')
        ->toContain('emails for your choir.')
        ->toContain('Unsubscribe from Attendance Reporting');
});
