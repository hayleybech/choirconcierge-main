<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Singer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Inertia\Testing\Assert;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function login_displays_the_login_form(): void
    {
        $this->get(the_tenant_route('login'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page
                ->component('Auth/Login'));
    }

    /** @test */
    public function invalid_login_displays_validation_errors(): void
    {
        $this->post(the_tenant_route('login'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_authenticates_and_redirects_user(): void
    {
        $user = User::factory()->create();

        $this->post(the_tenant_route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(the_tenant_route('dash'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function logout_deauthenticates_and_redirects_user(): void
    {
        // login
        $user = User::factory()->has(Singer::factory())->create();

        $this->post(the_tenant_route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // logout
        $this->post(the_tenant_route('logout'))
            ->assertRedirect(the_tenant_route('dash'));

        $this->assertGuest();
    }
}
