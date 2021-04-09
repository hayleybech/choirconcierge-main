<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\User;
use App\Models\UserGroup;
use Database\Seeders\Dummy\DummyUserGroupSeeder;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DummyUserSeeder::class);
        $this->seed(DummyUserGroupSeeder::class);
    }

    /** @test */
    public function index_for_admin_returns_list_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('groups.index'));

        $response->assertStatus(200);
        $response->assertViewIs('groups.index');
    }

    /** @test */
    public function index_for_unauthorised_returns_redirect(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('groups.index'));

        $response->assertRedirect();
    }

    /** @test */
    public function index_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(the_tenant_route('groups.index'));

        $response->assertRedirect();
    }

    /** @test */
    public function create_for_admin_returns_create_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('groups.create'));

        $response->assertOk();
        $response->assertViewIs('groups.create');
    }

    /** @test */
    public function create_for_unauthorised_returns_redirect(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('groups.create'));

        $response->assertRedirect();
    }

    /** @test */
    public function create_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(the_tenant_route('groups.create'));

        $response->assertRedirect();
    }

    /** @test */
    public function store_for_admin_creates_a_group(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $data = [
            'title' => $title,
            'slug' => Str::slug($title),
            'list_type' => 'chat',
        ];
        $response = $this->post(the_tenant_route('groups.store'), $data);

        $response->assertRedirect(); // @todo assert redirect to groups.show (with ID)?
        $this->assertDatabaseHas('user_groups', $data);
    }

    /** @test */
    public function store_for_unauthorised_doesnt_create_a_group(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $data = [
            'title' => $title,
            'slug' => Str::slug($title),
            'list_type' => 'chat',
        ];
        $response = $this->post(the_tenant_route('groups.store'), $data);

        $response->assertRedirect(the_tenant_route('dash'));
        $this->assertDatabaseMissing('user_groups', $data);
    }

    /** @test */
    public function store_for_anon_doesnt_create_a_group(): void
    {
        $this->assertGuest();

        $title = $this->faker->sentence;
        $data = [
            'title' => $title,
            'slug' => Str::slug($title),
            'list_type' => 'chat',
        ];
        $response = $this->post(the_tenant_route('groups.store'), $data);

        $response->assertRedirect(the_tenant_route('login'));
        $this->assertDatabaseMissing('user_groups', $data);
    }

    /** @test */
    public function show_for_admin_returns_show_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('groups.show', $group));

        $response->assertViewIs('groups.show');
        $response->assertOk();
    }

    /** @test */
    public function show_for_unauthorised_returns_redirect(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('groups.show', ['group' => $group]));

        $response->assertRedirect(the_tenant_route('dash'));
    }

    /** @test */
    public function show_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('groups.show', ['group' => $group]));

        $response->assertRedirect(the_tenant_route('login'));
    }

    /** @test */
    public function edit_for_admin_returns_edit_view(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('groups.edit', ['group' => $group]));

        $response->assertOk();
        $response->assertViewIs('groups.edit');
    }

    /** @test */
    public function edit_for_unauthorised_returns_redirect(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('groups.edit', ['group' => $group]));

        $response->assertRedirect(the_tenant_route('dash'));
    }

    /** @test */
    public function edit_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->get(the_tenant_route('groups.edit', ['group' => $group]));

        $response->assertRedirect(the_tenant_route('login'));
    }

    /** @test */
    public function update_for_admin_changes_group(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $data = [
            'title' => $title,
            'slug' => Str::slug($title),
            'list_type' => 'chat',
        ];
        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->put(the_tenant_route('groups.update', ['group' => $group]), $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('user_groups', $data);
    }

    /** @test */
    public function update_for_unauthorised_doesnt_change_group(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $data = [
            'title' => $title,
            'slug' => Str::slug($title),
            'list_type' => 'chat',
        ];
        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->put(the_tenant_route('groups.update', ['group' => $group]), $data);

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_groups', $data);
    }

    /** @test */
    public function update_for_anon_doesnt_change_group(): void
    {
        $this->assertGuest();

        $title = $this->faker->sentence;
        $data = [
            'title' => $title,
            'slug' => Str::slug($title),
            'list_type' => 'chat',
        ];
        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->put(the_tenant_route('groups.update', ['group' => $group]), $data);

        $response->assertRedirect();
        $this->assertDatabaseMissing('user_groups', $data);
    }

    /** @test */
    public function destroy_for_admin_deletes_group(): void
    {
        $user = User::withRole('Admin')->first();
        $this->actingAs($user);

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->delete(the_tenant_route('groups.destroy', ['group' => $group]));

        $response->assertRedirect();
        $this->assertSoftDeleted('user_groups', ['id' => $group->id]);
    }

    /** @test */
    public function destroy_for_unauthorised_doesnt_delete_group(): void
    {
        $user = User::withoutRole('Admin')->first();
        $this->actingAs($user);

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->delete(the_tenant_route('groups.destroy', ['group' => $group]));

        $response->assertRedirect();
        $this->assertDatabaseHas('user_groups', ['id' => $group->id]);
    }

    /** @test */
    public function destroy_for_anon_doesnt_delete_group(): void
    {
        $this->assertGuest();

        $group = UserGroup::query()->inRandomOrder()->first();
        $response = $this->delete(the_tenant_route('groups.destroy', ['group' => $group]));

        $response->assertRedirect();
        $this->assertDatabaseHas('user_groups', ['id' => $group->id]);
    }
}
