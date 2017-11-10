<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
		
		/*
		DB::table('users')->insert([
            'name' => str_random(10),
            'email' => str_random(10).'@gmail.com',
            'password' => bcrypt('secret'),
        ]);*/
		
		$user = DB::table('users')->insert([
            'name' => 'Hayden',
            'email' => 'haydenbech@gmail.com',
            'password' => bcrypt('*tokra1#'),
        ]);
		
		$admin_role = DB::table('roles')->insert([
            ['name' => 'Admin'],
        ]);
		
		DB::table('users_roles')->insert([
            'user_id' => $user,
            'role_id' => $admin_role,
        ]);
    }
}
