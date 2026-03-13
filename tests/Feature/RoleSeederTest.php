<?php

namespace Tests\Feature;

use App\Models\Role;
use Database\Seeders\Critical\CriticalUserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_critical_user_seeder_assigns_correct_poll_permissions(): void
    {
        // Run the seeder
        $this->seed(CriticalUserSeeder::class);

        // Check Admin
        $admin = Role::where('name', 'Admin')->firstOrFail();
        $this->assertContains('polls_view', $admin->abilities);
        $this->assertContains('polls_create', $admin->abilities);
        $this->assertContains('polls_update', $admin->abilities);
        $this->assertContains('polls_delete', $admin->abilities);

        // Check Teams
        $teams = ['Music Team', 'Membership Team', 'Accounts Team', 'Uniforms Team', 'Events Team'];
        foreach ($teams as $teamName) {
            $team = Role::where('name', $teamName)->firstOrFail();
            $this->assertContains('polls_view', $team->abilities, "Team $teamName missing polls_view");
            $this->assertContains('polls_create', $team->abilities, "Team $teamName missing polls_create");
            $this->assertContains('polls_update', $team->abilities, "Team $teamName missing polls_update");
            $this->assertContains('polls_delete', $team->abilities, "Team $teamName missing polls_delete");
        }

        // Check User
        $user = Role::where('name', 'User')->firstOrFail();
        $this->assertContains('polls_view', $user->abilities);
        $this->assertNotContains('polls_create', $user->abilities);
        $this->assertNotContains('polls_update', $user->abilities);
        $this->assertNotContains('polls_delete', $user->abilities);
    }
}
