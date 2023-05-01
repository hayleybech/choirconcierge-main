<?php

namespace Database\Seeders\Dummy;

use App\Models\Role;
use App\Models\Singer;
use App\Models\Task;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DummyTaskSeeder extends Seeder
{
    public function run()
    {
        // Fetch roles
        $music_role = Role::where('name', 'Music Team')->pluck('id');
        $member_role = Role::where('name', 'Membership Team')->pluck('id');
        $accounts_role = Role::where('name', 'Accounts Team')->pluck('id');
        $uniforms_role = Role::where('name', 'Uniforms Team')->pluck('id');

        // Create tasks, with their roles
        DB::table('tasks')->insert([
            [
                'tenant_id' => tenant('id'),
                'name' => 'Member Profile',
                'role_id' => $member_role[0],
                'type' => 'manual',
                'route' => 'singers.index',
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Voice Placement',
                'role_id' => $music_role[0],
                'type' => 'form',
                'route' => 'singers.placements.create',
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Pass Audition',
                'role_id' => $music_role[0],
                'type' => 'manual',
                'route' => 'task.complete',
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Pay Fees',
                'role_id' => $accounts_role[0],
                'type' => 'manual',
                'route' => 'task.complete',
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Provide Uniform',
                'role_id' => $uniforms_role[0],
                'type' => 'manual',
                'route' => 'task.complete',
            ],
            [
                'tenant_id' => tenant('id'),
                'name' => 'Create Account',
                'role_id' => $member_role[0],
                'type' => 'manual',
                'route' => 'task.complete',
            ],
        ]);
    }
}
