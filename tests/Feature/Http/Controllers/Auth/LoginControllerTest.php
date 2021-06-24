<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class LoginControllerTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function login_displays_the_login_form(): void
	{
		$response = $this->get(the_tenant_route('login'));

		$response->assertStatus(200);
		$response->assertViewIs('auth.login');
	}

	/** @test */
	public function invalid_login_displays_validation_errors(): void
	{
		$response = $this->post(the_tenant_route('login'), []);

		$response->assertStatus(302);
		$response->assertSessionHasErrors('email');
	}

	/** @test */
	public function login_authenticates_and_redirects_user(): void
	{
		$user = User::factory()->create();

		$response = $this->post(the_tenant_route('login'), [
			'email' => $user->email,
			'password' => 'password',
		]);

		$response->assertRedirect(the_tenant_route('dash'));
		$this->assertAuthenticatedAs($user);
	}

	/** @test */
	public function logout_deauthenticates_and_redirects_user(): void
	{
		// login
		$user = User::factory()->create();

		$this->post(the_tenant_route('login'), [
			'email' => $user->email,
			'password' => 'password',
		]);

		// logout
		$response = $this->post(the_tenant_route('logout'));

		$response->assertRedirect(the_tenant_route('dash'));
		$this->assertGuest();
	}
}
