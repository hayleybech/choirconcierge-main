<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserGroupControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed(\UserTableSeeder::class);
        $this->seed(\UserGroupSeeder::class);
    }

    /** @test */
    public function index_for_admin_returns_list_view(): void
    {
        $user = User::whereHas('roles', static function(Builder $query) {
            $query->where('name', '=', 'Admin');
        })->first();
        $this->actingAs($user);

        $response = $this->get(route('groups.index'));

        $response->assertStatus(200);
        $response->assertViewIs('groups.index');
    }

    /** @test */
    public function index_for_unauthorised_returns_redirect(): void
    {
        $user = User::whereDoesntHave('roles', static function(Builder $query) {
            $query->where('name', '=', 'Admin');
        })->first();
        $this->actingAs($user);

        $response = $this->get(route('groups.index'));

        $response->assertRedirect();
    }

    /** @test */
    public function index_for_anon_returns_redirect(): void
    {
        $this->assertGuest();

        $response = $this->get(route('groups.index'));

        $response->assertRedirect();
    }
}
