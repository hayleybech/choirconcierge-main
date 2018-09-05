<?php

use App\Role;
use Illuminate\Database\Seeder;
use App\User;
use App\Task;

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
		
		
		$this->call('SingerTableSeeder');
		$this->command->info('User table seeded!');
		
		$this->call('TaskTableSeeder');
		$this->command->info('Task table seeded!');	
		
		$this->call('NotificationTemplateSeeder');
		$this->command->info('Notification Template table seeded!');	

    }
}
