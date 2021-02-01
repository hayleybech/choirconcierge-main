<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Folder;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\Dummy\DummyFolderSeeder;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FolderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(DummyUserSeeder::class);
        $this->seed(DummyFolderSeeder::class);
    }

    ////////////////////////////////////////////////////////////
    /// INDEX

    /** @test */
    public function index_for_employee_returns_list_view(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any promoted role is fine
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('folders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('folders.index');
    }

    /** @test */
    /*
    public function index_for_member_returns_list_view(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('folders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('folders.index');
    }*/

    /** @test */
    public function index_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(the_tenant_route('folders.index'));
        $response->assertRedirect(the_tenant_route('login'));
    }

    ///////////////////////////////////////////////////////////////
    /// CREATE

    /** @test */
    public function create_for_employee_returns_create_view(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $response = $this->get(the_tenant_route('folders.create'));

        $response->assertStatus(200);
        $response->assertViewIs('folders.create');
    }

    /** @test */
    /*
    public function create_for_member_returns_redirect(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $response = $this->get( the_tenant_route('folders.create') );

        $response->assertRedirect( the_tenant_route('dash') );
    }*/

    /** @test */
    public function create_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(the_tenant_route('folders.create'));
        $response->assertRedirect(the_tenant_route('login'));
    }

    ///////////////////////////////////////////////////////////
    /// STORE

    /** @test */
    public function store_for_employee_creates_a_folder(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $response = $this->post(the_tenant_route('folders.index'), [
            'title' => $title
        ]);

        $response->assertRedirect( the_tenant_route('folders.index', ['status' => 'Folder created. ']) );
        $this->assertDatabaseHas('folders', [
            'title' => $title,
        ]);
    }

    /** @test */
    /*
    public function store_for_member_returns_redirect(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $response = $this->post(the_tenant_route('folders.index'), [
            'title' => $title
        ]);

        $response->assertRedirect( the_tenant_route('dash' ) );
        $this->assertDatabaseMissing('folders', [
            'title' => $title,
        ]);
    }

    /** @test */
    public function store_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $title = $this->faker->sentence;
        $response = $this->post(the_tenant_route('folders.index'), [
            'title' => $title
        ]);

        $response->assertRedirect( the_tenant_route('login' ) );
        $this->assertDatabaseMissing('folders', [
            'title' => $title,
        ]);
    }

    ////////////////////////////////////////////////////////////////////
    /// SHOW

    /** @test */
    public function show_for_employee_returns_a_view(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('folders.show', ['folder' => $folder]) );

        $response->assertStatus(200);
        $response->assertViewIs('folders.show');
    }

    /** @test */
    /*
    public function show_for_member_returns_a_view(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('folders.show', ['folder' => $folder]) );

        $response->assertStatus(200);
        $response->assertViewIs('folders.show');
    }*/

    /** @test */
    public function show_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('folders.show', ['folder' => $folder]) );


        $response->assertRedirect( the_tenant_route('login') );
    }

    ///////////////////////////////////////////////////////
    /// EDIT

    /** @test */
    public function edit_for_employee_returns_a_view(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('folders.edit', ['folder' => $folder]) );

        $response->assertStatus(200);
        $response->assertViewIs('folders.edit');
    }

    /** @test */
    /*
    public function edit_for_member_returns_redirect(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('folders.edit', ['folder' => $folder]) );

        $response->assertRedirect( the_tenant_route('dash') );
    }*/

    /** @test */
    public function edit_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( the_tenant_route('folders.edit', ['folder' => $folder]) );

        $response->assertRedirect( the_tenant_route('login') );
    }

    /** @test */
    public function update_for_employee_changes_the_folder(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $title = $this->faker->sentence;
        $response = $this->put( the_tenant_route('folders.update', ['folder' => $folder]), [
            'title' => $title,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('folders', [
            'id'    => $folder->id,
            'title' => $title,
        ]);
    }

    /** @test */
    /*
    public function update_for_member_doesnt_change_the_folder(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $title = $this->faker->sentence;
        $response = $this->put( the_tenant_route('folders.update', ['folder' => $folder]), [
            'title' => $title,
        ]);

        $response->assertRedirect( the_tenant_route('dash') );
        $this->assertDatabaseMissing('folders', [
            'id'    => $folder->id,
            'title' => $title,
        ]);
    }*/

    /** @test */
    public function update_for_anon_doesnt_change_the_folder(): void
    {
        $this->assertGuest();

        $folder = Folder::query()->inRandomOrder()->first();
        $title = $this->faker->sentence;
        $response = $this->put( the_tenant_route('folders.update', ['folder' => $folder]), [
            'title' => $title,
        ]);

        $response->assertRedirect( the_tenant_route('login') );
        $this->assertDatabaseMissing('folders', [
            'id'    => $folder->id,
            'title' => $title,
        ]);
    }

    /** @test */
    public function destroy_for_employee_deletes_the_folder(): void
    {
        $user = Role::firstWhere('name', '!=', 'User')->users->first(); // Any role is fine
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->delete( the_tenant_route('folders.destroy', ['folder' => $folder]) );

        $response->assertStatus(302);
        $this->assertSoftDeleted('folders', [
            'id'    => $folder->id,
        ]);
    }

    /** @test */
    /*
    public function destroy_for_member_doesnt_delete_the_folder(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->delete( the_tenant_route('folders.destroy', ['folder' => $folder]) );

        $response->assertRedirect(the_tenant_route('dash'));
        $this->assertDatabaseHas('folders', [
            'id'    => $folder->id,
        ]);
    }*/
    
    /** @test */
    public function destroy_for_anon_doesnt_delete_the_folder(): void
    {
        $this->assertGuest();

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->delete( the_tenant_route('folders.destroy', ['folder' => $folder]) );

        $response->assertRedirect(the_tenant_route('login'));
        $this->assertDatabaseHas('folders', [
            'id'    => $folder->id,
        ]);
    }
}
