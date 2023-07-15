<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\Membership;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

	protected bool $tenancy = false;

    /** @test */
    public function login_displays_the_login_form(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Auth/Login'));
    }

    /** @test */
    public function invalid_login_displays_validation_errors(): void
    {
        $this->post(route('login'), [])
            ->assertStatus(302)
            ->assertSessionHasErrors('email');
    }

    /** @test */
    public function login_authenticates_and_redirects_user(): void
    {
        $user = User::factory()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertRedirect(route('central.default-dash.index'));

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function logout_deauthenticates_and_redirects_user(): void
    {
        // login
        $user = User::factory()->create();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // logout
        $this->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
