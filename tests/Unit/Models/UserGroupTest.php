<?php

namespace Tests\Unit\Models;

use App\Enums\SingerStatus;
use App\Models\Enrolment;
use App\Models\Ensemble;
use App\Models\Role;
use App\Models\Membership;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGroupTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_all_recipients_returns_directly_assigned_users(): void
    {
        $group = UserGroup::factory()->create();

        $users = User::factory()
            ->count(3)
            ->create();
        $group->recipient_users()->attach($users->pluck('id'));

        $this->assertCount(3, $group->get_all_recipients());
    }

    public function test_get_all_recipients_returns_users_for_roles(): void
    {
        $group = UserGroup::factory()->create();

        $roles = Role::factory()
            ->has(Membership::factory()->count(3), 'members')
            ->count(2)
            ->create();

        $group->recipient_roles()->attach($roles->pluck('id'));

        $this->assertCount(6, $group->get_all_recipients());
    }

    public function test_get_all_recipients_returns_users_for_voice_parts(): void
    {
        $group = UserGroup::factory()->create();

        $voice_parts = VoicePart::factory()
            ->has(Enrolment::factory()->count(3), 'enrolments')
            ->count(2)
            ->create();

        $group->recipient_voice_parts()->attach($voice_parts->pluck('id'));

        $this->assertCount(6, $group->get_all_recipients());
    }

    public function test_get_all_recipients_returns_users_for_categories(): void
    {
        $group = UserGroup::factory()->create();

        $statusValues = [SingerStatus::MEMBERS->value, SingerStatus::PROSPECTS->value];
        foreach ($statusValues as $status) {
            $group->recipient_singer_statuses()->create(['memberable_id' => $status, 'memberable_type' => SingerStatus::class]);
        }

        User::query()->delete();

        foreach ($statusValues as $statusValue) {
            User::factory()->count(3)->create()->each(function($user) use ($statusValue) {
                $membership = Membership::factory()->for($user)->create();
                $membership->statuses()->delete();
                $membership->statuses()->create(['status' => $statusValue]);
            });
        }

        $this->assertEquals(6, $group->get_all_recipients()->count(), 'Found recipients: ' . $group->get_all_recipients()->pluck('id')->implode(', '));
    }

    public function test_get_all_senders_returns_directly_assigned_users(): void
    {
        $group = UserGroup::factory()->create();

        $users = User::factory()
            ->count(3)
            ->create();
        $group->sender_users()->attach($users->pluck('id'));

        $this->assertCount(3, $group->get_all_senders());
    }

    public function test_get_all_senders_returns_users_for_roles(): void
    {
        $group = UserGroup::factory()->create();

        $roles = Role::factory()
            ->has(Membership::factory()->count(3), 'members')
            ->count(2)
            ->create();

        $group->sender_roles()->attach($roles->pluck('id'));

        $this->assertCount(6, $group->get_all_senders());
    }

    public function test_get_all_senders_returns_users_for_voice_parts(): void
    {
        $group = UserGroup::factory()->create();

        $voice_parts = VoicePart::factory()
            ->has(Enrolment::factory()->count(3), 'enrolments')
            ->count(2)
            ->create();

        $group->sender_voice_parts()->attach($voice_parts->pluck('id'));

        $this->assertCount(6, $group->get_all_senders());
    }

    public function test_get_all_senders_returns_users_for_categories(): void
    {
        $group = UserGroup::factory()->create();

        $statusValues = [SingerStatus::MEMBERS->value, SingerStatus::PROSPECTS->value];
        foreach ($statusValues as $status) {
            $group->sender_singer_statuses()->create(['sender_id' => $status, 'sender_type' => SingerStatus::class]);
        }

        User::query()->delete();

        foreach ($statusValues as $statusValue) {
            User::factory()->count(3)->create()->each(function($user) use ($statusValue) {
                $membership = Membership::factory()->for($user)->create();
                $membership->statuses()->delete();
                $membership->statuses()->create(['status' => $statusValue]);
            });
        }

        $this->assertEquals(6, $group->get_all_senders()->count());
    }

    public function test_get_all_recipients_works_for_the_correct_tenant(): void
    {
        $group = UserGroup::factory()->create();

        $users = User::factory()
            ->count(3)
            ->create();
        $group->recipient_users()->attach($users->pluck('id'));

        $tenant = Tenant::create('test-tenant-1', 'Test Tenant 1', 'Australia/Perth');
        $tenant->domains()->create(['domain' => $tenant->id]);
        tenancy()->initialize($tenant);

        $this->assertCount(3, $group->get_all_recipients());
    }

    public function test_get_all_senders_works_for_the_correct_tenant(): void
    {
        $group = UserGroup::factory()->create();

        $users = User::factory()
            ->count(3)
            ->create();
        $group->sender_users()->attach($users->pluck('id'));

        $tenant = Tenant::create('test-tenant-1', 'Test Tenant 1', 'Australia/Perth');
        $tenant->domains()->create(['domain' => $tenant->id]);
        tenancy()->initialize($tenant);

        $this->assertCount(3, $group->get_all_senders());
    }

    public function test_get_all_recipients_filters_by_ensemble(): void
    {
        $group = UserGroup::factory()->create();

        $role = Role::factory()->create();
        $statusValue = SingerStatus::MEMBERS->value;
        $ensembleA = Ensemble::factory()->create();
        $ensembleB = Ensemble::factory()->create();

        // 3 users in Role with Ensemble A
        User::factory()->count(3)->create()->each(function($user) use ($role, $statusValue, $ensembleA) {
            $membership = Membership::factory()->create([
                'user_id' => $user->id,
            ]);
            $membership->statuses()->create(['status' => $statusValue]);
            $membership->roles()->attach($role->id);
            Enrolment::factory()->create(['membership_id' => $membership->id, 'ensemble_id' => $ensembleA->id]);
        });

        // 2 users in Role with Ensemble B
        User::factory()->count(2)->create()->each(function($user) use ($role, $statusValue, $ensembleB) {
            $membership = Membership::factory()->create([
                'user_id' => $user->id,
            ]);
            $membership->statuses()->create(['status' => $statusValue]);
            $membership->roles()->attach($role->id);
            Enrolment::factory()->create(['membership_id' => $membership->id, 'ensemble_id' => $ensembleB->id]);
        });

        $group->recipient_roles()->attach($role->id);

        // Without ensemble filter, should have 5 recipients
        $this->assertEquals(5, $group->get_all_recipients()->count());

        // Filter by Ensemble A
        $group->recipient_ensembles()->attach($ensembleA->id);
        $group->load('recipient_ensembles'); // Refresh relation cache

        $this->assertEquals(3, $group->get_all_recipients()->count());

        // Filter by Ensemble A and B
        $group->recipient_ensembles()->attach($ensembleB->id);
        $group->load('recipient_ensembles');
        $this->assertEquals(5, $group->get_all_recipients()->count());
    }

    public function test_get_all_senders_filters_by_ensemble(): void
    {
        $group = UserGroup::factory()->create();

        $role = Role::factory()->create();
        $statusValue = SingerStatus::MEMBERS->value;
        $ensembleA = Ensemble::factory()->create();
        $ensembleB = Ensemble::factory()->create();

        // 3 users in Role with Ensemble A
        User::factory()->count(3)->create()->each(function($user) use ($role, $statusValue, $ensembleA) {
            $membership = Membership::factory()->create([
                'user_id' => $user->id,
            ]);
            $membership->statuses()->create(['status' => $statusValue]);
            $membership->roles()->attach($role->id);
            Enrolment::factory()->create(['membership_id' => $membership->id, 'ensemble_id' => $ensembleA->id]);
        });

        // 2 users in Role with Ensemble B
        User::factory()->count(2)->create()->each(function($user) use ($role, $statusValue, $ensembleB) {
            $membership = Membership::factory()->create([
                'user_id' => $user->id,
            ]);
            $membership->statuses()->create(['status' => $statusValue]);
            $membership->roles()->attach($role->id);
            Enrolment::factory()->create(['membership_id' => $membership->id, 'ensemble_id' => $ensembleB->id]);
        });

        $group->sender_roles()->attach($role->id);

        // Without ensemble filter, should have 5 senders
        $this->assertEquals(5, $group->get_all_senders()->count());

        // Filter by Ensemble A
        $group->sender_ensembles()->attach($ensembleA->id);
        $group->load('sender_ensembles'); // Refresh relation cache

        $this->assertEquals(3, $group->get_all_senders()->count());

        // Filter by Ensemble A and B
        $group->sender_ensembles()->attach($ensembleB->id);
        $group->load('sender_ensembles');
        $this->assertEquals(5, $group->get_all_senders()->count());
    }
}
