<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FolderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(\UserTableSeeder::class);
        $this->seed(\FolderSeeder::class);
    }

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

    /** @test */
    public function create_for_employee_returns_create_view(): void
    {
        $user = Role::first()->users->first(); // Any role is fine
        $this->actingAs($user);

        $response = $this->get(route('folders.create'));

        $response->assertStatus(200);
        $response->assertViewIs('folders.create');
    }

    // @todo Finish this test. Not working currently due to middleware weirdness.
    /** @test */
    /*public function create_for_member_returns_redirect(): void
    {
        \Log::debug('test');
        $this->withoutExceptionHandling();

        $user = User::query()->whereDoesntHave('roles')->first();
        $this->actingAs($user);

        $response = $this->get(route('folders.index'));
        $response->assertRedirect(route('dash'));
    }*/

    /** @test */
    public function create_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(route('folders.create'));
        $response->assertRedirect(route('login'));
    }

    // @todo store for employee creates a folder
    // @todo store for member returns a redirect
    // @todo store for guest returns a redirect

    // @todo show for employee returns a view
    // @todo show for member returns a view
    // @todo show for guest returns a redirect

    // @todo edit for employee returns a view
    // @todo edit for member returns a redirect
    // @todo edit for guest returns a redirect

    // @todo update for employee changes the folder
    // @todo update for member doesnt change the folder
    // @todo update for guest doesnt change the folder

    // @todo destroy for employee deletes the folder
    // @todo destroy for member doesnt delete the folder
    // @todo destroy for guest doesnt delete the folder
}
