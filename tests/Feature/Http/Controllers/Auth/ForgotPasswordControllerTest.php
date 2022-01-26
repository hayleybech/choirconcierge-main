<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Password;
use Inertia\Testing\Assert;
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
		$this->get(the_tenant_route('password.request', []))
            ->assertOk()
            ->assertInertia(fn(Assert $page) => $page
                ->component('Auth/ForgotPassword'));
	}

	// @todo test send_reset_link_btn_sends_reset_email
}
