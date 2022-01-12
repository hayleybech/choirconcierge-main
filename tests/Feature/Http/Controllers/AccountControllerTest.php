<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Singer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\AccountController
 */
class AccountControllerTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/**
	 * @test
	 */
	public function edit_renders_the_template(): void
	{
		$this->actingAs(User::factory()->has(Singer::factory())->create());

		$response = $this->get(the_tenant_route('accounts.edit'));

		$response->assertOk();
		$response->assertViewIs('accounts.edit');
		$response->assertViewHas('user');
	}

	/**
	 * @test
	 * @dataProvider profileProvider
	 */
	public function update_saves_the_user_details($getData): void
	{
        $this->actingAs(User::factory()->has(Singer::factory())->create());

		$data = $getData();
		$response = $this->post(the_tenant_route('accounts.update'), $data);

		$response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', Arr::except($data, [
            'password_confirmation',
        ]));
		$response->assertRedirect(the_tenant_route('accounts.edit'));
	}

	public function profileProvider(): array
	{
		return [
			[
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
			],
		];
	}
}
