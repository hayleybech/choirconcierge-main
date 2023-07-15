<?php
use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

/** @see \App\Http\Controllers\AccountController */

uses(RefreshDatabase::class, WithFaker::class);

test('edit@ renders the template', function() {
    actingAs(User::factory()->has(Membership::factory())->create());

    get(the_tenant_route('accounts.edit'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('Account/Edit')
            ->has('user'));
});

test('update@ saves the user details', function ($data) {
    $user = User::factory()->has(Membership::factory())->create();
    actingAs($user);

    post(the_tenant_route('accounts.update'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('singers.show', $user->membership));

    assertDatabaseHas('users', Arr::except($data, ['password_confirmation', 'password']));
})->with('profiles');

test('update@ saves the user password', function ($data) {
    $user = User::factory()->has(Membership::factory())->create();
    actingAs($user);

    post(the_tenant_route('accounts.update'), $data)
        ->assertSessionHasNoErrors()
        ->assertRedirect(the_tenant_route('singers.show', $user->membership));

    assertDatabaseHas('users', Arr::except($data, ['password_confirmation', 'password']));

    $user->refresh();
    expect(Hash::check($data['password'], $user->password))->toBeTrue();
})->with('profiles');

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
