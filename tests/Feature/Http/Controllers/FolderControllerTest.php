<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Folder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FolderControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(\UserTableSeeder::class);
        $this->seed(\FolderSeeder::class);
    }

    ////////////////////////////////////////////////////////////
    /// INDEX

    /** @test */
    public function index_for_employee_returns_list_view(): void
    {
        $user = Role::first()->users->first(); // Any role is fine
        $this->actingAs($user);

        $response = $this->get(route('folders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('folders.index');
    }

    /** @test */
    public function index_for_member_returns_list_view(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $response = $this->get(route('folders.index'));

        $response->assertStatus(200);
        $response->assertViewIs('folders.index');
    }

    /** @test */
    public function index_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(route('folders.index'));
        $response->assertRedirect(route('login'));
    }

    ///////////////////////////////////////////////////////////////
    /// CREATE

    /** @test */
    public function create_for_employee_returns_create_view(): void
    {
        $user = Role::first()->users->first(); // Any role is fine
        $this->actingAs($user);

        $response = $this->get(route('folders.create'));

        $response->assertStatus(200);
        $response->assertViewIs('folders.create');
    }

    /** @test */
    public function create_for_member_returns_redirect(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $response = $this->get( route('folders.create') );

        $response->assertRedirect( route('dash') );
    }

    /** @test */
    public function create_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(route('folders.create'));
        $response->assertRedirect(route('login'));
    }

    ///////////////////////////////////////////////////////////
    /// STORE

    /** @test */
    public function store_for_employee_creates_a_folder(): void
    {
        $user = Role::first()->users->first(); // Any role is fine
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $response = $this->post(route('folders.index'), [
            'title' => $title
        ]);

        $response->assertRedirect( route('folders.index', ['status' => 'Folder created. ']) );
        $this->assertDatabaseHas('folders', [
            'title' => $title,
        ]);
    }

    /** @test */
    public function store_for_member_returns_redirect(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $title = $this->faker->sentence;
        $response = $this->post(route('folders.index'), [
            'title' => $title
        ]);

        $response->assertRedirect( route('dash' ) );
        $this->assertDatabaseMissing('folders', [
            'title' => $title,
        ]);
    }

    /** @test */
    public function store_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $title = $this->faker->sentence;
        $response = $this->post(route('folders.index'), [
            'title' => $title
        ]);

        $response->assertRedirect( route('login' ) );
        $this->assertDatabaseMissing('folders', [
            'title' => $title,
        ]);
    }

    ////////////////////////////////////////////////////////////////////
    /// SHOW

    /** @test */
    public function show_for_employee_returns_a_view(): void
    {
        $user = Role::first()->users->first(); // Any role is fine
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( route('folders.show', ['folder' => $folder]) );

        $response->assertStatus(200);
        $response->assertViewIs('folders.show');
    }

    /** @test */
    public function show_for_member_returns_a_view(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( route('folders.show', ['folder' => $folder]) );

        $response->assertStatus(200);
        $response->assertViewIs('folders.show');
    }

    public function show_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( route('folders.show', ['folder' => $folder]) );


        $response->assertRedirect( route('login') );
    }

    ///////////////////////////////////////////////////////
    /// EDIT

    /** @test */
    public function edit_for_employee_returns_a_view(): void
    {
        $user = Role::first()->users->first(); // Any role is fine
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( route('folders.edit', ['folder' => $folder]) );

        $response->assertStatus(200);
        $response->assertViewIs('folders.edit');
    }

    public function edit_for_member_returns_redirect(): void
    {
        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( route('folders.edit', ['folder' => $folder]) );

        $response->assertRedirect( route('dash') );
    }

    public function edit_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $folder = Folder::query()->inRandomOrder()->first();
        $response = $this->get( route('folders.edit', ['folder' => $folder]) );

        $response->assertRedirect( route('login') );
    }

    // @todo update for employee changes the folder
    // @todo update for member doesnt change the folder
    // @todo update for guest doesnt change the folder

    // @todo destroy for employee deletes the folder
    // @todo destroy for member doesnt delete the folder
    // @todo destroy for guest doesnt delete the folder

    // @todo update these tests to use file uploads - whoops!
}
