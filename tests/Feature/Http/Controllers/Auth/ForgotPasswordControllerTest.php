<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

/**
 * @see ForgotPasswordController
 */
class ForgotPasswordControllerTest extends TestCase
{
	use RefreshDatabase;

	/** @test */
	public function forgot_password_displays_forgot_form(): void
	{
		$response = $this->get(the_tenant_route('password.request', []));

		$response->assertStatus(200);
		$response->assertViewIs('auth.passwords.email');
	}

	// @todo test send_reset_link_btn_sends_reset_email
}
