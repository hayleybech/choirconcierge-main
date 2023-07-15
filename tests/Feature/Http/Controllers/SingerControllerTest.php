<?php

namespace Tests\Feature\Http\Controllers;

use App\Mail\Welcome;
use App\Models\Role;
use App\Models\Membership;
use App\Models\Task;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Testing\AssertableInertia;
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

        $this->get(the_tenant_route('singers.create'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Create')
                ->has('voice_parts')
                ->has('roles')
            );
    }

    /**
     * @test
     */
    public function destroy_redirects_to_index(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $membership = Membership::factory()->create();

        $this->delete(the_tenant_route('singers.destroy', [$membership]))
            ->assertRedirect(route('singers.index'));

        $this->assertSoftDeleted($membership);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();

        $this->get(the_tenant_route('singers.edit', [$singer]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Edit')
                ->has('singer')
                ->has('voice_parts')
                ->has('roles')
            );
    }

    /**
     * @test
     */
    public function index_returns_singers(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $this->get(the_tenant_route('singers.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Index')
                ->has('allSingers')
                ->has('statuses')
                ->has('defaultStatus')
                ->has('voiceParts')
                ->has('roles')
            );
    }

    /**
     * @test
     */
    public function show_returns_an_ok_response(): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();

        $this->get(the_tenant_route('singers.show', [$singer]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Singers/Show')
                ->has('singer')
                ->has('categories')
            );
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
        $response = $this->post(the_tenant_route('singers.store'), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', Arr::only($data, [
            'first_name',
            'last_name',
            'email',
        ]));
        $this->assertDatabaseHas('memberships', Arr::only($data, [
            'onboarding_enabled',
            'joined_at',
            'reason_for_joining',
            'referrer',
            'membership_details',
        ]));

        $user = User::firstWhere('email', $data['email']);
        $this->assertDatabaseMissing('memberships_tasks', [
            'membership_id' => $user->membership->id,
            'task_id' => $task->id,
        ]);

        $response->assertRedirect(the_tenant_route('singers.show', [$user->membership]));
        $mail->assertSent(Welcome::class);
    }

    /**
     * @test
     * @dataProvider singerProvider
     */
    public function store_creates_a_user_when_given_an_email($getData): void
    {
        $mail = Mail::fake();

        $this->actingAs($this->createUserWithRole('Membership Team'));

        $data = $getData();
        $response = $this->post(the_tenant_route('singers.store'), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('users', Arr::only($data, [
            'first_name',
            'last_name',
            'email',
        ]));
        $this->assertDatabaseHas('memberships', Arr::only($data, [
            'onboarding_enabled',
            'joined_at',
            'reason_for_joining',
            'referrer',
            'membership_details',
        ]));

        $user = User::firstWhere('email', $data['email']);

        $response->assertRedirect(the_tenant_route('singers.show', [$user->membership]));
        $mail->assertSent(Welcome::class);
    }

    /**
     * @test
     * @dataProvider singerProvider
     */
    public function store_assigns_an_existing_user_when_given_a_user_id($getData): void
    {
        $mail = Mail::fake();

        $user = User::factory()->create();

        $this->actingAs($this->createUserWithRole('Membership Team'));

        $data = $getData();
        $data['email'] = null;
        $data['user_id'] = $user->id;
        $response = $this->post(the_tenant_route('singers.store'), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('memberships', array_merge(
            ['user_id' => $user->id],
            Arr::only($data, [
                'onboarding_enabled',
                'joined_at',
                'reason_for_joining',
                'referrer',
                'membership_details',
            ])
        ));

        $user->refresh();

        $response->assertRedirect(the_tenant_route('singers.show', [$user->membership]));
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
        $data['onboarding_disabled'] = false;
        $response = $this->post(the_tenant_route('singers.store'), $data);

        $user = User::firstWhere('email', $data['email']);
        $this->assertDatabaseHas('memberships_tasks', [
            'membership_id' => $user->membership->id,
            'task_id' => $task->id,
        ]);

        $response->assertRedirect(the_tenant_route('singers.show', [$user->membership]));
        $mail->assertSent(Welcome::class);
    }

    /**
     * @test
     * @dataProvider singerProvider
     */
    public function update_saves_the_singer_fields($getData): void
    {
        $this->actingAs($this->createUserWithRole('Membership Team'));

        $singer = Membership::factory()->create();

        $data = $getData();
        $response = $this->put(the_tenant_route('singers.update', [$singer]), $data)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('memberships', Arr::only($data, [
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
    public function update_saves_the_singer_roles($getData): void
    {
        $this->actingAs($this->createUserWithRole('Admin'));

        $singer = Membership::factory()->create();

        $roles = Role::pluck('id')->random(2);

        $data = $getData();
        $response = $this->put(the_tenant_route('singers.update', [$singer]), array_merge($data, [
            'user_roles' => $roles->all(),
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => $singer->id,
            'role_id' => $roles[0],
        ]);
        $this->assertDatabaseHas('memberships_roles', [
            'membership_id' => $singer->id,
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

        $singer = Membership::factory()->create();

        $part = VoicePart::pluck('id')->random(1)[0];

        $data = $getData();
        $response = $this->put(the_tenant_route('singers.update', [$singer]), array_merge($data, [
            'voice_part_id' => $part,
        ]));

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('memberships', [
            'id' => $singer->id,
            'voice_part_id' => $part,
        ]);
    }

	/**
	 * @test
	 * @dataProvider singerProvider
	 */
	public function update_saves_the_financial_details($getData): void
	{
		$this->withoutExceptionHandling();

		$this->actingAs($this->createUserWithRole('Accounts Team'));

		$singer = Membership::factory()->create();

		$expiryDate = now();

		$data = $getData();
		$response = $this->put(the_tenant_route('singers.update', [$singer]), array_merge($data, [
			'paid_until' => $expiryDate,
		]));

		$response->assertSessionHasNoErrors();
		$this->assertDatabaseHas('memberships', [
			'id' => $singer->id,
			'paid_until' => $expiryDate,
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
                        'onboarding_disabled' => true,
                        'reason_for_joining' => $this->faker->sentence(),
                        'referrer' => $this->faker->sentence(),
                        'membership_details' => $this->faker->sentence(),

                        // User
                        'first_name' => $this->faker->firstName(),
                        'last_name' => $this->faker->lastName(),
                        'email' => $this->faker->email(),
                        'password' => $password,
                        'password_confirmation' => $password,
                    ];
                },
            ],
        ];
    }
}
