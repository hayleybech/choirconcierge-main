<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Profile;
use App\Models\Singer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function create_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

	    $singer = Singer::factory()->create();

        $response = $this->get(the_tenant_route('singers.profiles.create', [$singer]));

        $response->assertOk();
        $response->assertViewIs('singers.createprofile');
        $response->assertViewHas('singer');
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()
	        ->has(Profile::factory())
	        ->create();

        $response = $this->get(the_tenant_route('singers.profiles.edit', [$singer, $singer->profile]));

        $response->assertOk();
        $response->assertViewIs('singers.editprofile');
        $response->assertViewHas('singer');
        $response->assertViewHas('profile');
    }

    /**
     * @test
     * @dataProvider profileProvider
     */
    public function store_redirects_to_singer($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()->create();

        $data = $getData();
        $response = $this->post(the_tenant_route('singers.profiles.store', [$singer]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('profiles', $data);
        $response->assertRedirect(the_tenant_route('singers.show', $singer));
    }

    /**
     * @test
     * @dataProvider profileProvider
     */
    public function update_redirects_to_singer($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

	    $singer = Singer::factory()
		    ->has(Profile::factory())
		    ->create();

	    $data = $getData();
        $response = $this->put(the_tenant_route('singers.profiles.update', [$singer, $singer->profile]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('profiles', $data);
        $response->assertRedirect(the_tenant_route('singers.show', $singer));
    }

	public function profileProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();
					return [
						'dob'                   => Carbon::instance($this->faker->dateTimeBetween('-100 years', '-5 years'))->format('Y-m-d'),
						'phone'                 => $this->faker->phoneNumber(),
						'ice_name'              => $this->faker->name(),
						'ice_phone'             => $this->faker->phoneNumber(),
						'address_street_1'      => $this->faker->streetAddress(),
						'address_street_2'      => $this->faker->secondaryAddress(),
						'address_suburb'        => $this->faker->city(),
						'address_state'         => $this->faker->stateAbbr(),
						'address_postcode'      => $this->faker->numerify('####'),
						'reason_for_joining'    => $this->faker->sentence(),
						'referrer'              => $this->faker->sentence(),
						'profession'            => $this->faker->sentence(),
						'skills'                => $this->faker->sentence(),
						'height'                => $this->faker->randomFloat(2, 0, 300),
						'membership_details'    => $this->faker->sentence(),
					];
				}
			]
		];
	}
}
