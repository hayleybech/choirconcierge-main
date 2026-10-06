<?php
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;

/** @see \App\Http\Controllers\AccountController */

uses(RefreshDatabase::class, WithFaker::class);

test('edit@ renders the template', function() {
    actingAs(User::factory()->has(Membership::factory())->create());

    get(the_tenant_route('account.edit'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Account/Edit')
            ->has('user'));
});

it('provides two-factor settings to the account page', function () {
    $user = User::factory()->has(Membership::factory())->create();
    $user->createTwoFactorAuth();
    $user->refresh();
    $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());

    actingAs($user)
        ->get(the_tenant_route('account.edit'))
        ->assertInertia(fn ($page) => $page
            ->where('two_factor_enabled', true)
            ->has('recovery_codes')
        );
});

test('two-factor settings use the tenant account route', function () {
    $user = User::factory()->has(Membership::factory())->create();
    actingAs($user);

    get(the_tenant_route('account.two-factor.show'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Account/TwoFactor')
            ->where('enabled', false));
});

test('update@ saves the user details', function ($data) {
    $user = User::factory()->has(Membership::factory())->create();
    actingAs($user);

    patch(the_tenant_route('account.update'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('account.edit'));

    assertDatabaseHas('users', Arr::except($data, ['password_confirmation', 'password']));
})->with('profiles');

test('update@ saves the user password', function ($data) {
    $user = User::factory()->has(Membership::factory())->create();
    actingAs($user);

    patch(the_tenant_route('account.update'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('account.edit'));

    assertDatabaseHas('users', Arr::except($data, ['password_confirmation', 'password']));

    $user->refresh();
    expect(Hash::check($data['password'], $user->password))->toBeTrue();
})->with('profiles');

test('update@ saves measurement and calendar preferences', function () {
    $user = User::factory()->has(Membership::factory())->create();
    actingAs($user);

    patch(the_tenant_route('account.update'), [
        'first_name' => $user->first_name,
        'last_name' => $user->last_name,
        'email' => $user->email,
        'prefers_metric' => false,
        'first_day_of_week' => 0,
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'prefers_metric' => false,
        'first_day_of_week' => 0,
    ]);
});

test('update@ saves security settings without requiring profile fields', function () {
    $user = User::factory()->has(Membership::factory())->create();
    actingAs($user);

    patch(the_tenant_route('account.update'), [
        'tab' => 'security',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ])->assertSessionHasNoErrors();

    $user->refresh();
    expect(Hash::check('new-password', $user->password))->toBeTrue();
});

test('update@ saves language settings without changing profile fields', function () {
    $user = User::factory()->has(Membership::factory())->create(['prefers_metric' => false]);
    actingAs($user);

    patch(the_tenant_route('account.update'), [
        'tab' => 'language',
        'prefers_metric' => true,
        'first_day_of_week' => 0,
    ])->assertSessionHasNoErrors();

    assertDatabaseHas('users', [
        'id' => $user->id,
        'prefers_metric' => true,
        'first_day_of_week' => 0,
    ]);
});

test('central account update redirects back to account settings', function () {
    $user = User::factory()->create();
    actingAs($user);

    patch(route('central.account.update'), [
        'tab' => 'language',
        'prefers_metric' => true,
        'first_day_of_week' => 0,
    ])
        ->assertSessionHasNoErrors()
        ->assertRedirect(route('central.account.edit'));
});

test('measurement system falls back to the users address country', function () {
    expect(User::factory()->create(['address_country' => 'US'])->usesImperialMeasurements())->toBeTrue()
        ->and(User::factory()->create(['address_country' => 'AU'])->usesImperialMeasurements())->toBeFalse();
});

test('update@ can upload an avatar using POST with method spoofing', function () {
    Storage::fake('public');

    $user = User::factory()->has(Membership::factory())->create([
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
    ]);
    actingAs($user);

    $file = UploadedFile::fake()->image('avatar.jpg');

    $response = $this->post(the_tenant_route('account.update'), [
        '_method' => 'PUT',
        'first_name' => 'John',
        'last_name' => 'Doe',
        'email' => 'john@example.com',
        'avatar' => $file,
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect(the_tenant_route('account.edit'));

    $user->refresh();
    expect($user->getMedia('avatar'))->not->toBeEmpty();
});

dataset('profiles', [
    function () {
        $this->setUpFaker();
        $password = Str::random(8);

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'pronouns' => $this->faker->sentence(),
            'email' => $this->faker->email(),
            'password' => $password,
            'password_confirmation' => $password,
            'dob' => Carbon::instance($this->faker->dateTimeBetween('-100 years', '-5 years'))->format(
                'Y-m-d',
            ),
            'phone' => $this->faker->phoneNumber(),
            'ice_name' => $this->faker->name(),
            'ice_phone' => $this->faker->phoneNumber(),
            'address_street_1' => $this->faker->streetAddress(),
            'address_street_2' => $this->faker->secondaryAddress(),
            'address_suburb' => $this->faker->city(),
            'address_state' => $this->faker->stateAbbr(),
            'address_postcode' => $this->faker->numerify('####'),
            'profession' => $this->faker->sentence(),
            'skills' => $this->faker->sentence(),
            'height' => $this->faker->randomFloat(2, 0, 300),
            'bha_id' => $this->faker->numerify('####'),
        ];
    },
]);
