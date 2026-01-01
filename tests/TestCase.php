<?php

namespace Tests;

use App\Models\Role;
use App\Models\Membership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;
use Laravel\Paddle\Subscription;

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
        Subscription::where('billable_id', 'phpunit')->delete();

        $tenant = Tenant::factory()
            ->withSubscription(planId: 62775)
            ->withDomain()
            ->create(['id' => 'phpunit', 'name' => 'PHPUnit Testing', 'timezone' => 'Australia/Perth']);

        tenancy()->initialize($tenant);
    }

    protected function actingAsRole(string $roleName): User
    {
        return tap($this->createUserWithRole($roleName), fn ($user) => $this->actingAs($user));
    }

    protected function createUserWithRole(string $roleName): User
    {
        $singer = Membership::factory()->create();
        $singer->roles()->attach([Role::where('name', $roleName)->valueOrFail('id')]);

        return $singer->user;
    }
}
