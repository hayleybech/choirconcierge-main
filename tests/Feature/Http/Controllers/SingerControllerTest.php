<?php

namespace Tests\Feature\Http\Controllers;

use App\Mail\Welcome;
use App\Models\Role;
use App\Models\Singer;
use App\Models\Task;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
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
	public function store_inserts_the_singer($getData): void
	{
		$task = Task::factory()->create();
		$mail = Mail::fake();

		$this->actingAs($this->createUserWithRole('Membership Team'));

		$data = $getData();
		$response = $this->post(the_tenant_route('singers.store'), $data);

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users', Arr::except($data, [
            'onboarding_enabled',
            'joined_at',
            'reason_for_joining',
            'referrer',
            'membership_details',
            'password',
            'password_confirmation',
        ]));
        $this->assertDatabaseHas('singers', Arr::only($data, [
            'onboarding_enabled',
            'joined_at',
            'reason_for_joining',
            'referrer',
            'membership_details',
        ]));

        $user = User::firstWhere('email', $data['email']);
		$this->assertDatabaseMissing('singers_tasks', [
			'singer_id' => $user->singer->id,
			'task_id' => $task->id,
		]);

		$response->assertRedirect(the_tenant_route('singers.show', [$user->singer]));
		$mail->assertSent(Welcome::class);
	}

	/**
	 * @test
	 * @dataProvider singerProvider
	 */
	public function store_inserts_tasks_for_prospects($getData): void
	{
		$task = Task::factory()->create();
		$mail = Mail::fake();

		$this->actingAs($this->createUserWithRole('Membership Team'));

		$data = $getData();
		$data['onboarding_enabled'] = true;
		$response = $this->post(the_tenant_route('singers.store'), $data);

		$user = User::firstWhere('email', $data['email']);
		$this->assertDatabaseHas('singers_tasks', [
			'singer_id' => $user->singer->id,
			'task_id' => $task->id,
		]);

		$response->assertRedirect(the_tenant_route('singers.show', [$user->singer]));
		$mail->assertSent(Welcome::class);
	}

	/**
	 * @test
	 * @dataProvider singerProvider
	 */
	public function update_saves_the_singer_fields($getData): void
	{
		$this->actingAs($this->createUserWithRole('Membership Team'));

		$singer = Singer::factory()->create();

		$data = $getData();
		$response = $this->put(the_tenant_route('singers.update', [$singer]), $data);

		$response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('singers', Arr::only($data, [
            'reason_for_joining',
            'referrer',
            'membership_details',
            'joined_at',
            'onboarding_enabled',
        ]));
		$response->assertRedirect(the_tenant_route('singers.show', [$singer]));
	}

    /**
     * @test
     * @dataProvider singerProvider
     */
    public function update_saves_the_user_roles($getData): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()->create();

        $roles = Role::pluck('id')->random(2);

        $data = $getData();
        $response = $this->put(the_tenant_route('singers.update', [$singer]), array_merge($data, [
            'user_roles' => $roles->all(),
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('users_roles', [
            'user_id' => $singer->user->id,
            'role_id' => $roles[0],
        ]);
        $this->assertDatabaseHas('users_roles', [
            'user_id' => $singer->user->id,
            'role_id' => $roles[1],
        ]);
    }

    /**
     * @test
     * @dataProvider singerProvider
     */
    public function update_saves_the_voice_part($getData): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Singer::factory()->create();

        $part = VoicePart::pluck('id')->random(1)[0];

        $data = $getData();
        $response = $this->put(the_tenant_route('singers.update', [$singer]), array_merge($data, [
            'voice_part_id' => $part,
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('singers', [
            'id' => $singer->id,
            'voice_part_id' => $part,
        ]);
    }

	public function singerProvider(): array
	{
		return [
			[
				function () {
					$this->setUpFaker();
                    $password = Str::random(8);
					return [
					    // Singer
                        'onboarding_enabled' => false,
                        'reason_for_joining' => $this->faker->sentence(),
                        'referrer' => $this->faker->sentence(),
                        'membership_details' => $this->faker->sentence(),

                        // User
                        'first_name' => $this->faker->firstName(),
                        'last_name' => $this->faker->lastName(),
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
