<?php

namespace Tests;

use App\Models\Role;
use App\Models\Singer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use JMac\Testing\Traits\AdditionalAssertions;
use App\Models\Tenant;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
	use CreatesApplication, AdditionalAssertions;

	protected bool $tenancy = true;

	protected function setUp(): void
	{
		parent::setUp();

//        RefreshDatabaseState::$migrated = false;

		if ($this->tenancy) {
			$this->initializeTenancy();
		}
	}

	public function initializeTenancy(): void
	{
		Tenant::find('phpunit')?->delete();
		$tenant = Tenant::create(id: 'phpunit', choir_name: 'PHPUnit Testing', timezone: 'Australia/Perth');
		$tenant->domains()->create(['domain' => 'phpunit']);
		$tenant->save();

		tenancy()->initialize($tenant);
	}

	protected function createUserWithRole(string $roleName): User
	{
		$singer = Singer::factory()->create();
		$singer->user->roles()->attach([Role::where('name', $roleName)->value('id')]);

		return $singer->user;
	}
}
