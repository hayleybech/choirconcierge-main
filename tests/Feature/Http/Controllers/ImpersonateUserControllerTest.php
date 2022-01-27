<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Singer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SwitchTenantController
 */
class ImpersonateUserControllerTest extends TestCase
{
	use RefreshDatabase;

	/**
	 * @test
	 */
	public function start_authenticates_as_target(): void
	{
		$this->actingAs($this->createUserWithRole('Admin'));

		$user = User::factory()->create();

		$response = $this->get(the_tenant_route('users.impersonate', [$user]));
		$response->assertRedirect();

		$this->assertAuthenticatedAs($user);
	}

	/**
	 * @test
	 */
	public function stop_authenticates_as_original(): void
	{
		// Start
		$user1 = $this->createUserWithRole('Admin');
		$this->actingAs($user1);

		$user2 = User::factory()->has(Singer::factory())->create();

		$response = $this->get(the_tenant_route('users.impersonate', [$user2]));
		$response->assertRedirect();

		$this->assertAuthenticatedAs($user2);

		// Stop
		$response = $this->get(the_tenant_route('impersonation.stop'));
		$response->assertRedirect();

		$this->assertAuthenticatedAs($user1);
	}
}
