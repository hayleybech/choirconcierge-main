<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\Singer;
use App\Models\User;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class SingerControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DummyUserSeeder::class);
    }

    // INDEX

    /** @test */
    public function index_for_employee_returns_list_view(): void {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('singers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('singers.index');
    }

    /** @test */
    /*
    public function index_for_member_returns_list_view(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('singers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('singers.index');
    }*/

    /** @test */
    public function index_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(the_tenant_route('singers.index'));
        $response->assertRedirect(the_tenant_route('login'));
    }

    // CREATE
    /** @test */
    public function create_for_membership_or_music_team_returns_create_view(): void
    {
        $user = User::withRoles(['Membership Team', 'Music Team'])
            ->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('singers.create'));

        $response->assertStatus(200);
        $response->assertViewIs('singers.create');
    }

    /** @test */
    public function create_for_unauthorised_returns_redirect(): void
    {
        $user = User::withoutRoles(['Membership Team', 'Music Team'])
            ->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('singers.create'));

        $response->assertRedirect();
    }

    /** @test */
    public function create_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(the_tenant_route('singers.create'));

        $response->assertRedirect();
    }

    // STORE
    /** @test */
    public function store_for_membership_or_music_team_creates_a_singer(): void
    {
        $user = User::withRoles(['Membership Team', 'Music Team'])
            ->first();
        $this->actingAs($user);

        $password = Str::random(8);
        $data  = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'onboarding_enabled' => $this->faker->boolean(10),
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $response = $this->post(the_tenant_route('singers.store'), $data);

        $response->assertRedirect(); // @todo assert redirect to singers.show (with ID)?

        $this->assertDatabaseHas('singers', [
            'name' => $data['name'],
            'email' => $data['email'],
            'onboarding_enabled' => $data['onboarding_enabled'],
        ]);
    }

    /** @test */
    public function store_for_unauthorised_doesnt_create_a_singer(): void
    {
        $user = User::withoutRoles(['Membership Team', 'Music Team'])
            ->first();
        $this->actingAs($user);

        $password = Str::random(8);
        $data  = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'onboarding_enabled' => $this->faker->boolean(10),
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $response = $this->post(the_tenant_route('singers.store'), $data);

        $response->assertRedirect(the_tenant_route('dash'));
        $this->assertDatabaseMissing('singers', [
            'name' => $data['name'],
            'email' => $data['email'],
            'onboarding_enabled' => $data['onboarding_enabled'],
        ]);
    }

    /** @test */
    public function store_for_anon_doesnt_create_a_singer(): void
    {
        $this->assertGuest();

        $password = Str::random(8);
        $data  = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'onboarding_enabled' => $this->faker->boolean(10),
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $response = $this->post(the_tenant_route('singers.store'), $data);

        $response->assertRedirect(the_tenant_route('login'));
        $this->assertDatabaseMissing('singers', [
            'name' => $data['name'],
            'email' => $data['email'],
            'onboarding_enabled' => $data['onboarding_enabled'],
        ]);
    }

    // SHOW
    /** @test */
    public function show_for_employee_returns_show_view(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('singers.show', $singer) );

        $response->assertViewIs('singers.show');
        $response->assertOk();
    }

    /** @test */
    /*
    public function show_for_member_returns_show_view(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('singers.show', $singer) );

        $response->assertViewIs('singers.show');
        $response->assertOk();
    }*/

    /** @test */
    public function show_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('singers.show', $singer) );

        $response->assertRedirect(the_tenant_route('login'));
    }

    /** @test */
    public function edit_for_membership_team_returns_edit_view(): void
    {
        $user = User::withRoles(['Membership Team', 'Music Team'])->first();
        $this->actingAs($user);

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('singers.edit', ['singer' => $singer]) );

        $response->assertOk();
        $response->assertViewIs('singers.edit');
    }

    /** @test */
    public function edit_for_unauthorised_returns_redirect(): void
    {
        $user = User::withoutRoles(['Membership Team', 'Music Team'])->first();
        $this->actingAs($user);

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('singers.edit', ['singer' => $singer]) );

        $response->assertRedirect(the_tenant_route('dash'));
    }

    /** @test */
    public function edit_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('singers.edit', ['singer' => $singer]) );

        $response->assertRedirect(the_tenant_route('login'));
    }

    // UPDATE
    /** @test */
    public function update_for_membership_team_changes_singer(): void
    {
        $user = User::withRoles(['Membership Team', 'Music Team'])->first();
        $this->actingAs($user);

        $data  = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'onboarding_enabled' => $this->faker->boolean(10),
        ];
        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->put( the_tenant_route('singers.update', ['singer' => $singer]), $data );

        $response->assertRedirect();
        $this->assertDatabaseHas('singers', $data);
    }

    /** @test */
    public function update_for_unauthorised_doesnt_change_singer(): void
    {
        $user = User::withoutRoles(['Membership Team', 'Music Team'])->first();
        $this->actingAs($user);

        $data  = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'onboarding_enabled' => $this->faker->boolean(10),
        ];
        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->put( the_tenant_route('singers.update', ['singer' => $singer]), $data );

        $response->assertRedirect(the_tenant_route('dash'));
        $this->assertDatabaseMissing('singers', $data);
    }

    /** @test */
    public function update_for_anon_doesnt_change_singer(): void
    {
        $this->assertGuest();

        $data  = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'onboarding_enabled' => $this->faker->boolean(10),
        ];
        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->put( the_tenant_route('singers.update', ['singer' => $singer]), $data );

        $response->assertRedirect(the_tenant_route('login'));
        $this->assertDatabaseMissing('singers', $data);
    }

    // DESTROY
    /** @test */
    public function destroy_for_admin_deletes_singer(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->delete( the_tenant_route('singers.destroy', ['singer' => $singer]) );

        $response->assertRedirect();
        $this->assertDatabaseMissing('singers', ['id' => $singer->id]);
    }

    /** @test */
    public function destroy_for_unauthorised_doesnt_delete_singer(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->delete( the_tenant_route('singers.destroy', ['singer' => $singer]) );

        $response->assertRedirect();
        $this->assertDatabaseHas('singers', ['id' => $singer->id]);
    }

    /** @test */
    public function destroy_for_anon_doesnt_delete_singer(): void
    {
        $this->assertGuest();

        $singer = Singer::query()->inRandomOrder()->first();
        $response = $this->delete( the_tenant_route('singers.destroy', ['singer' => $singer]) );

        $response->assertRedirect();
        $this->assertDatabaseHas('singers', ['id' => $singer->id]);
    }
}
