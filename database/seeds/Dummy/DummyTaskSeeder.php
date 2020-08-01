<?php

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\Singer;
use App\Models\Role;

class DummyTaskSeeder extends Seeder {
	
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
			'name'		=> 'Member Profile', 
			'role_id'	=> $member_role[0],
			'type'		=> 'form',
			'route'		=> 'profile.create',
			],
			[
			'name'		=> 'Voice Placement', 
			'role_id'	=> $music_role[0],
			'type'		=> 'form',
			'route'		=> 'placement.create',
			],
			[
			'name'		=> 'Pass Audition',
			'role_id'	=> $music_role[0],
			'type'		=> 'manual',
			'route'		=> 'task.complete',
			],
			[
			'name'		=> 'Pay Fees', 
			'role_id'	=> $accounts_role[0],
			'type'		=> 'manual',
			'route'		=> 'task.complete',
			],
			[
			'name'		=> 'Provide Uniform', 
			'role_id'	=> $uniforms_role[0],
			'type'		=> 'manual',
			'route'		=> 'task.complete',
			],
			[
			'name'		=> 'Create Account', 
			'role_id'	=> $member_role[0],
			'type'		=> 'manual',
			'route'		=> 'task.complete',
			],
		]);
		
		// Attach all tasks to all singers
		$tasks = Task::all();
		$singers = DB::table('singers')->pluck('id');
		
		foreach($tasks as $task){
			$task->singers()->attach($singers);
		}
		
	}
}