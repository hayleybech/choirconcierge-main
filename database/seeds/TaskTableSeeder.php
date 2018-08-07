<?php

use Illuminate\Database\Seeder;
use App\Task;
use App\Singer;
use App\Role;

class TaskTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('tasks')->delete();
		
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
			'route'		=> 'singer.memberprofile',
			],
			[
			'name'		=> 'Voice Placement', 
			'role_id'	=> $music_role[0],
			'type'		=> 'form',
			'route'		=> 'singer.voiceplacement',
			],
			[
			'name'		=> 'Pass Audition',
			'role_id'	=> $music_role[0],
			'type'		=> 'manual',
			'route'		=> '',
			],
			[
			'name'		=> 'Pay Fees', 
			'role_id'	=> $accounts_role[0],
			'type'		=> 'manual',
			'route'		=> '',
			],
			[
			'name'		=> 'Provide Uniform', 
			'role_id'	=> $uniforms_role[0],
			'type'		=> 'manual',
			'route'		=> '',
			],
			[
			'name'		=> 'Create Account', 
			'role_id'	=> $member_role[0],
			'type'		=> 'manual',
			'route'		=> '',
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