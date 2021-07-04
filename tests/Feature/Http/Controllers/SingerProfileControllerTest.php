<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Profile;
use App\Models\Singer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SingerProfileController
 */
class SingerProfileControllerTest extends TestCase
{
	use RefreshDatabase, WithFaker;

	/**
	 * @test
	 */
	public function edit_returns_an_ok_response(): void
	{
		$this->actingAs($this->createUserWithRole('Membership Team'));

		$singer = Singer::factory()
			->has(User::factory())
			->create();

		$response = $this->get(the_tenant_route('singers.profiles.edit', [$singer]));

		$response->assertOk();
		$response->assertViewIs('singers.editprofile');
		$response->assertViewHas('singer');
		$response->assertViewHas('user');
	}

	/**
	 * @test
	 * @dataProvider profileProvider
	 */
	public function update_redirects_to_singer($getData): void
	{
		$this->actingAs($this->createUserWithRole('Membership Team'));

		$singer = Singer::factory()
			->has(User::factory())
			->create();

		$data = $getData();
		$response = $this->put(the_tenant_route('singers.profiles.update', [$singer]), $data);

		$response->assertSessionHasNoErrors();
		$this->assertDatabaseHas('singers', Arr::only($data, [
		    'reason_for_joining',
            'referrer',
            'membership_details',
        ]));
        $this->assertDatabaseHas('users', Arr::except($data, [
            'reason_for_joining',
            'referrer',
            'membership_details',
        ]));
		$response->assertRedirect(the_tenant_route('singers.show', $singer));
	}

	public function profileProvider(): array
	{
		return [
			[
				function () {
					$this->setUpFaker();
					return [
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

                        'reason_for_joining' => $this->faker->sentence(),
                        'referrer' => $this->faker->sentence(),
                        'membership_details' => $this->faker->sentence(),
					];
				},
			],
		];
	}
}
