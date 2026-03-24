<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Laragear\TwoFactor\Facades\Auth2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected bool $tenancy = false;

    public function test_user_can_view_two_factor_settings()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('central.account.two-factor'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Account/TwoFactor')
            ->has('qr_code')
            ->where('enabled', false)
        );
    }

    public function test_user_can_enable_two_factor_authentication()
    {
        $user = User::factory()->create();
        
        // Generate secret first as index does
        $this->actingAs($user)->get(route('central.account.two-factor'));
        
        $user->refresh();
        $secret = $user->twoFactorAuth;
        $code = $secret->makeCode();

        $response = $this->actingAs($user)->post(route('central.account.two-factor'), [
            'code' => $code,
        ]);

        $response->assertRedirect();
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_user_can_disable_two_factor_authentication()
    {
        $user = User::factory()->create();
        $user->createTwoFactorAuth();
        $user->refresh();
        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());

        $this->assertTrue($user->hasTwoFactorEnabled());

        $response = $this->actingAs($user)->delete(route('central.account.two-factor'));

        $response->assertRedirect();
        $this->assertFalse($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_user_is_redirected_to_2fa_challenge_after_login_if_enabled()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);
        $user->createTwoFactorAuth();
        $user->refresh();
        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('auth.2fa.challenge'));
        $this->assertFalse(auth()->check());
        $this->assertEquals($user->id, session('2fa.id'));
    }

    public function test_user_can_authenticate_with_2fa_code()
    {
        $user = User::factory()->create();
        $user->createTwoFactorAuth();
        $user->refresh();
        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());

        session(['2fa.id' => $user->id]);

        $code = $user->twoFactorAuth->makeCode();

        $response = $this->post(route('auth.2fa.challenge'), [
            'code' => $code,
        ]);

        $response->assertRedirect(config('auth.redirectTo', '/app/default-dash'));
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }

    public function test_user_can_authenticate_with_recovery_code()
    {
        $user = User::factory()->create();
        $user->createTwoFactorAuth();
        $user->refresh();
        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());
        
        $recoveryCode = $user->getRecoveryCodes()[0];

        session(['2fa.id' => $user->id]);

        $response = $this->post(route('auth.2fa.challenge'), [
            'recovery_code' => $recoveryCode,
        ]);

        $response->assertRedirect(config('auth.redirectTo', '/app/default-dash'));
        $this->assertTrue(auth()->check());
        $this->assertEquals($user->id, auth()->id());
    }
}
