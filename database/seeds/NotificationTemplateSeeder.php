<?php

use Illuminate\Database\Seeder;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tasks')->delete();
		
		// Create some dummy templates
		DB::table('notification_templates')->insert([
			[
			'task_id'		=> 1,
			'subject'		=> 'Singer Completed Member Profile',
			'recipients'	=> 'user:1',
			'body'			=> 
				'Hi %%user.name%%,
				A member profile has been completed for %%singer.name%%. ',
			'delay'			=> 0,
			],
			[
			'task_id'		=> 1,
			'subject'		=> 'Welcome to The Blenders!',
			'recipients'	=> 'singer:1',
			'body'			=> 
				'Hi %%singer.name%%,
				Welcome to The Blenders! You have 4 weeks to learn your audition songs. 
				You can download your songs [here].
				Regards,
				The Blenders',
			'delay'			=> 0,
			],
		]);
    }
}
