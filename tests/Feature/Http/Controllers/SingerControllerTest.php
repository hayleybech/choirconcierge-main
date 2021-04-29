<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Singer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SingerController
 */
class SingerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * @test
     */
    public function create_returns_an_ok_response(): void
    {
    	$this->actingAs($this->createUserWithRole('Membership Team'));

        $response = $this->get(the_tenant_route('singers.create'));

        $response->assertOk();
        $response->assertViewIs('singers.create');
        $response->assertViewHas('voice_parts');
        $response->assertViewHas('roles');
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()->create();

        $response = $this->delete(the_tenant_route('singers.destroy', [$singer]));

        $response->assertRedirect(route('singers.index'));
        $this->assertSoftDeleted($singer);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()->create();

        $response = $this->get(the_tenant_route('singers.edit', [$singer]));

        $response->assertOk();
        $response->assertViewIs('singers.edit');
        $response->assertViewHas('singer');
        $response->assertViewHas('voice_parts');
        $response->assertViewHas('roles');
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

        $response = $this->get(the_tenant_route('singers.index'));

        $response->assertOk();
        $response->assertViewIs('singers.index');
        $response->assertViewHas('all_singers');
        $response->assertViewHas('active_singers');
        $response->assertViewHas('member_singers');
        $response->assertViewHas('prospect_singers');
        $response->assertViewHas('archived_singers');
        $response->assertViewHas('filters');
        $response->assertViewHas('sorts');
        $response->assertViewHas('categories');
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()->create();

        $response = $this->get(the_tenant_route('singers.show', [$singer]));

        $response->assertOk();
        $response->assertViewIs('singers.show');
        $response->assertViewHas('singer');
        $response->assertViewHas('categories');
    }

    /**
     * @test
     * @dataProvider singerProvider
     */
    public function store_redirects_to_show($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

	    $data = $getData();
        $response = $this->post(the_tenant_route('singers.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('singers', [
        	'first_name'            => $data['first_name'],
	        'last_name'             => $data['last_name'],
	        'email'                 => $data['email'],
	        'onboarding_enabled'    => $data['onboarding_enabled'],
        ]);

        $singer = Singer::firstWhere('email', $data['email']);
        $response->assertRedirect(the_tenant_route('singers.show', [$singer]));
    }

    /**
     * @test
     * @dataProvider singerProvider
     */
    public function update_redirects_to_show($getData): void
    {
	    $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()->create();

	    $data = $getData();
        $response = $this->put(the_tenant_route('singers.update', [$singer]), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('singers', [
	        'first_name'            => $data['first_name'],
	        'last_name'             => $data['last_name'],
	        'email'                 => $data['email'],
	        'onboarding_enabled'    => $data['onboarding_enabled'],
        ]);
        $response->assertRedirect(the_tenant_route('singers.show', [$singer]));
    }

	public function singerProvider(): array
	{
		return [
			[
				function() {
					$this->setUpFaker();
					$password = Str::random(8);
					return [
						'first_name' => $this->faker->firstName(),
						'last_name' => $this->faker->lastName(),
						'email' => $this->faker->email(),
						'onboarding_enabled' => $this->faker->boolean(10),
						'password' => $password,
						'password_confirmation' => $password,
					];
				}
			]
		];
	}
}
