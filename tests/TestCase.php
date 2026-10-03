<?php

namespace Tests;

use App\Enums\SingerStatus;
use App\Models\Role;
use App\Models\Membership;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\RefreshDatabaseState;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use JMac\Testing\Traits\AdditionalAssertions;
use Illuminate\Support\Carbon;
use Laravel\Paddle\Subscription;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, AdditionalAssertions, RefreshDatabase;

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

    /**
     * @param array<array{SingerStatus, string}> $statuses Status history as [status, date] pairs.
     */
    protected function createMembershipWithStatusHistory(array $statuses): Membership
    {
        $membership = Membership::factory()->create();
        $membership->statuses()->delete();

        foreach ($statuses as [$status, $date]) {
            $membership->statuses()->create([
                'status' => $status->value,
                'created_at' => Carbon::parse($date),
                'updated_at' => Carbon::parse($date),
            ]);
        }

        return $membership->refresh();
    }

    protected function createUserWithRole(string $roleName): User
    {
        $singer = Membership::factory()->create();
        $singer->roles()->attach([Role::where('name', $roleName)->valueOrFail('id')]);

        return $singer->user;
    }
}
