<?php

namespace Tests;

use App\Models\Role;
use App\Models\Singer;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;

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
        $tenant = Tenant::create(id: 'phpunit', name: 'PHPUnit Testing', timezone: 'Australia/Perth');
        $tenant->domains()->create(['domain' => 'phpunit']);
        $tenant->save();

        tenancy()->initialize($tenant);
    }

    protected function actingAsRole(string $roleName): User
    {
        return tap($this->createUserWithRole($roleName), fn ($user) => $this->actingAs($user));
    }

    protected function createUserWithRole(string $roleName): User
    {
        $singer = Singer::factory()->create();
        $singer->roles()->attach([Role::where('name', $roleName)->value('id')]);

        return $singer->user;
    }
}
