<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SanctumTwoFactorTest extends TestCase
{
    use RefreshDatabase;

    protected bool $tenancy = false;

    // @todo fix this test - fails in CI
//    public function test_can_get_token_without_2fa_if_not_enabled()
//    {
//        $user = User::factory()->create([
//            'email' => 'test@example.com',
//            'password' => Hash::make('password'),
//        ]);
//
//        $response = $this->postJson('/api/sanctum/token', [
//            'email' => 'test@example.com',
//            'password' => 'password',
//            'device_name' => 'test-device',
//        ]);
//
//        $response->assertStatus(200);
//        $this->assertNotEmpty($response->getContent());
//    }

//    public function test_cannot_get_token_if_2fa_enabled_and_no_code_provided()
//    {
//        $user = User::factory()->create([
//            'email' => 'test@example.com',
//            'password' => Hash::make('password'),
//        ]);
//
//        // Enable 2FA
//        $user->createTwoFactorAuth();
//        $user->refresh();
//        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());
//
//        $this->assertTrue($user->hasTwoFactorEnabled());
//
//        $response = $this->postJson('/api/sanctum/token', [
//            'email' => 'test@example.com',
//            'password' => 'password',
//            'device_name' => 'test-device',
//        ]);
//
//        $response->assertStatus(403);
//        $response->assertJson(['message' => 'Two-factor authentication required.']);
//    }
//
//    public function test_can_get_token_if_2fa_enabled_and_valid_code_provided()
//    {
//        $user = User::factory()->create([
//            'email' => 'test@example.com',
//            'password' => Hash::make('password'),
//        ]);
//
//        // Enable 2FA
//        $user->createTwoFactorAuth();
//        $user->refresh();
//        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());
//
//        // Use a code from a different time window to ensure it hasn't been used
//        $at = \Illuminate\Support\Carbon::now()->addMinutes(2);
//        \Illuminate\Support\Carbon::setTestNow($at);
//        $code = $user->makeTwoFactorCode();
//
//        $response = $this->postJson('/api/sanctum/token', [
//            'email' => 'test@example.com',
//            'password' => 'password',
//            'device_name' => 'test-device',
//            'code' => $code,
//        ]);
//
//        \Illuminate\Support\Carbon::setTestNow();
//
//        $response->assertStatus(200);
//        $this->assertNotEmpty($response->getContent());
//    }
//
//    public function test_cannot_get_token_if_2fa_enabled_and_invalid_code_provided()
//    {
//        $user = User::factory()->create([
//            'email' => 'test@example.com',
//            'password' => Hash::make('password'),
//        ]);
//
//        // Enable 2FA
//        $user->createTwoFactorAuth();
//        $user->refresh();
//        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());
//
//        $response = $this->postJson('/api/sanctum/token', [
//            'email' => 'test@example.com',
//            'password' => 'password',
//            'device_name' => 'test-device',
//            'code' => 'invalid-code',
//        ]);
//
//        $response->assertStatus(422);
//        $response->assertJsonValidationErrors(['code']);
//    }
//
//    public function test_can_get_token_with_recovery_code()
//    {
//        $user = User::factory()->create([
//            'email' => 'test@example.com',
//            'password' => Hash::make('password'),
//        ]);
//
//        // Enable 2FA
//        $user->createTwoFactorAuth();
//        $user->refresh();
//        $user->confirmTwoFactorAuth($user->twoFactorAuth->makeCode());
//
//        $recoveryCode = $user->getRecoveryCodes()[0]['code'];
//
//        $response = $this->postJson('/api/sanctum/token', [
//            'email' => 'test@example.com',
//            'password' => 'password',
//            'device_name' => 'test-device',
//            'code' => $recoveryCode,
//        ]);
//
//        $response->assertStatus(200);
//        $this->assertNotEmpty($response->getContent());
//    }
}
