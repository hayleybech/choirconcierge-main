<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Http\Controllers\Auth\ForgotPasswordController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
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
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Auth/ForgotPassword'));
    }

    // @todo test send_reset_link_btn_sends_reset_email
}
