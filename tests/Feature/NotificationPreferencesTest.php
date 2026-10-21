<?php

use App\Enums\EmailCategory;
use App\Models\Membership;
use App\Models\Event;
use App\Models\Song;
use App\Models\User;
use App\Notifications\EventCreated;
use App\Notifications\SongUploaded;
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
                'system' => true,
                'product' => true,
                'promotions' => false,
                'community' => false,
            ]));
});

test('users can save account-wide email preferences', function () {
    $user = User::factory()->has(Membership::factory())->create();
    $this->actingAs($user);
    $preferences = EmailCategory::defaults();
    $preferences['songs'] = false;
    $preferences['events'] = false;
    $preferences['product'] = false;
    $preferences['promotions'] = true;

    $this->put(the_tenant_route('account.notifications.update'), [
        'email_preferences' => $preferences,
    ])->assertSessionHasNoErrors();

    expect($user->fresh()->emailPreferences())->toBe($preferences);
});

test('mandatory email categories cannot be disabled', function () {
    $user = User::factory()->has(Membership::factory())->create();
    $this->actingAs($user);
    $preferences = EmailCategory::defaults();
    $preferences['communications'] = false;
    $preferences['system'] = false;

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
