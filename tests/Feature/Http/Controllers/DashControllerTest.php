<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Role;
use Database\Seeders\Dummy\DummyUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\DashController
 */
class DashControllerTest extends TestCase
{
	/**
	 * @test
	 */
	public function index_returns_an_ok_response(): void
	{
		$this->actingAs($this->createUserWithRole('Membership Team')); // Any role is fine

		$response = $this->get(the_tenant_route('dash'));

		$response->assertOk();
		$response->assertViewIs('dash');
		$response->assertViewHas('birthdays');
		$response->assertViewHas('empty_dobs');
		$response->assertViewHas('songs');
		$response->assertViewHas('events');
	}
}
