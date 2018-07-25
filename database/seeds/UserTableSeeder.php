<?php

use Illuminate\Database\Seeder;
use App\Role;
use App\Singer;

class UserTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('users')->delete();
		DB::table('roles')->delete();
		
		$user = User::create([
			'name' => 'Hayden',
            'email' => 'haydenbech@gmail.com',
            'password' => bcrypt('*tokra1#'),
		]);
		
		DB::table('roles')->insert([
            ['name' => 'Admin'],
            ['name' => 'Music Team'],
            ['name' => 'Membership Team'],
            ['name' => 'Accounts Team'],
            ['name' => 'Uniforms Team'],
        ]);
		
		$roles = Role::all()->pluck('id')->toArray();
		
		$user->roles()->attach($roles);
	}