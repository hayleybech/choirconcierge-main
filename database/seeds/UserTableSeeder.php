<?php

use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Singer;
use Carbon\Carbon;

class UserTableSeeder extends Seeder
{

    public function run(): void
    {
        /*
         * STEP 0 - Clear
         */
        DB::table('users')->delete();
        DB::table('roles')->delete();
        DB::table('singers')->delete();
        DB::table('singers_tasks')->delete();
        DB::table('singer_categories')->delete();

        /*
         * STEP 1 - Insert initial real data
         */

        // Insert user roles
        DB::table('roles')->insert([
            ['name' => 'Admin'],
            ['name' => 'Music Team'],
            ['name' => 'Membership Team'],
            ['name' => 'Accounts Team'],
            ['name' => 'Uniforms Team'],
        ]);

        // Insert singer categories
        DB::table('singer_categories')->insert([
            ['name' => 'Prospects', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Archived Prospects', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Members', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['name' => 'Archived Members', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
        $singer_categories = SingerCategory::all();

        // Step 2 - Add dummy users - no roles
        factory(User::class, 30)->create()->each(static function(User $user) use ($singer_categories) {
            $faker = \Faker\Factory::create();

            // Step 2a - Create matching singer
            $user->singer()->create([
                'name' => $user->name,
                'email' => $user->email,
                'onboarding_enabled' => $faker->boolean(30),
            ]);

            // Step 2b - Attach random singer category
            UserTableSeeder::attachRandomSingerCategory($user->singer, $singer_categories);

            // Step 2c - Generate profile and placement for singer
            // @todo Seed singer profile and voice placement

            // Step 2d - Generate tasks
            // @todo Generate tasks for dummy singers
        });


        // Step 3 - Add admin
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

    public static function attachRandomSingerCategory(Singer $singer, Collection $categories): void
    {
        $category = $categories->random(1)->first();
        $singer->category()->associate($category);
        $singer->save();
    }
}