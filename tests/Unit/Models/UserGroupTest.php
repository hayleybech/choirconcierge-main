<?php

namespace Tests\Unit\Models;

use App\Models\Role;
use App\Models\Singer;
use App\Models\SingerCategory;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\VoicePart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserGroupTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function get_all_recipients_returns_directly_assigned_users(): void
    {
        $group = UserGroup::factory()->create();

        $users = User::factory()
            ->count(3)
            ->create();
        $group->recipient_users()->attach($users->pluck('id'));

        $this->assertCount(3, $group->get_all_recipients());
    }

    /** @test */
    public function get_all_recipients_returns_users_for_roles(): void
    {
        $group = UserGroup::factory()->create();

        $roles = Role::factory()
            ->has(Singer::factory()->count(3))
            ->count(2)
            ->create();

        $group->recipient_roles()->attach($roles->pluck('id'));

        $this->assertCount(6, $group->get_all_recipients());
    }

    /** @test */
    public function get_all_recipients_returns_users_for_voice_parts(): void
    {
        $group = UserGroup::factory()->create();

        $voice_parts = VoicePart::factory()
            ->has(Singer::factory()->count(3))
            ->count(2)
            ->create();

        $group->recipient_voice_parts()->attach($voice_parts->pluck('id'));

        $this->assertCount(6, $group->get_all_recipients());
    }

    /** @test */
    public function get_all_recipients_returns_users_for_categories(): void
    {
        $group = UserGroup::factory()->create();

        $categories = SingerCategory::factory()
            ->has(Singer::factory()->count(3))
            ->count(2)
            ->create();

        $group->recipient_singer_categories()->attach($categories->pluck('id'));

        $this->assertCount(6, $group->get_all_recipients());
    }

    /** @test */
    public function get_all_senders_returns_directly_assigned_users(): void
    {
        $group = UserGroup::factory()->create();

        $users = User::factory()
            ->count(3)
            ->create();
        $group->sender_users()->attach($users->pluck('id'));

        $this->assertCount(3, $group->get_all_senders());
    }

    /** @test */
    public function get_all_senders_returns_users_for_roles(): void
    {
        $group = UserGroup::factory()->create();

        $roles = Role::factory()
            ->has(Singer::factory()->count(3))
            ->count(2)
            ->create();

        $group->sender_roles()->attach($roles->pluck('id'));

        $this->assertCount(6, $group->get_all_senders());
    }

    /** @test */
    public function get_all_senders_returns_users_for_voice_parts(): void
    {
        $group = UserGroup::factory()->create();

        $voice_parts = VoicePart::factory()
            ->has(Singer::factory()->count(3))
            ->count(2)
            ->create();

        $group->sender_voice_parts()->attach($voice_parts->pluck('id'));

        $this->assertCount(6, $group->get_all_senders());
    }

    /** @test */
    public function get_all_senders_returns_users_for_categories(): void
    {
        $group = UserGroup::factory()->create();

        $categories = SingerCategory::factory()
            ->has(Singer::factory()->count(3))
            ->count(2)
            ->create();

        $group->sender_singer_categories()->attach($categories->pluck('id'));

        $this->assertCount(6, $group->get_all_senders());
    }

    /** @test */
    public function get_all_recipients_works_for_the_correct_tenant(): void
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

    /** @test */
    public function get_all_senders_works_for_the_correct_tenant(): void
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
}