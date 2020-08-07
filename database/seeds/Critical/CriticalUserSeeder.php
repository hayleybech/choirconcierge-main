<?php

use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Singer;
use Carbon\Carbon;
use Faker\Factory as Faker;

class  CriticalUserSeeder extends Seeder
{

    public function run(): void
    {
        /*
         * STEP 1 - Insert categories
         */

        // Insert user roles
        DB::table('roles')->insert([
            ['tenant_id' => tenant('id'), 'name' => 'Admin'],
            ['tenant_id' => tenant('id'), 'name' => 'Music Team'],
            ['tenant_id' => tenant('id'), 'name' => 'Membership Team'],
            ['tenant_id' => tenant('id'), 'name' => 'Accounts Team'],
            ['tenant_id' => tenant('id'), 'name' => 'Uniforms Team'],
        ]);

        // Insert singer categories
        DB::table('singer_categories')->insert([
            ['tenant_id' => tenant('id'), 'name' => 'Prospects', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['tenant_id' => tenant('id'), 'name' => 'Archived Prospects', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['tenant_id' => tenant('id'), 'name' => 'Members', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['tenant_id' => tenant('id'), 'name' => 'Archived Members', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
        $singer_categories = SingerCategory::all();


        /*
         * STEP 2 - Insert Admin
         */
        $user = User::create([
            'name' => 'Hayden',
            'email' => 'haydenbech@gmail.com',
            'password' => bcrypt('*tokra1#'),
        ]);
        $roles = Role::all()->pluck('id')->toArray();
        $user->roles()->attach($roles);

        // Create matching singer for admin
        $user->singer()->create([
            'name' => $user->name,
            'email' => $user->email,
            'onboarding_enabled' => 0,
        ]);

        // Attach admin to member category
        $cat_member = $singer_categories->firstWhere('name', '=', 'Members');
        $user->singer->category()->associate($cat_member);
        $user->singer->save();
    }
}