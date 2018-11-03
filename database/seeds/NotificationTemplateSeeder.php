<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notification_templates')->delete();
		
		// Create some dummy templates
		DB::table('notification_templates')->insert([
			[
            'task_id'		=> 1,
            'subject'		=> 'Singer Completed Member Profile',
            'recipients'	=> 'user:1',
            'body'			=>
"Hi %%user.name%%,
A member profile has been completed for %%singer.name%%. ",
            'delay'			=> 0,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
			],
			[
            'task_id'		=> 1,
            'subject'		=> 'Welcome to The Blenders!',
            'recipients'	=> 'singer:1',
            'body'			=>
"Hi %%singer.name%%,
Welcome to The Blenders! You have 4 weeks to learn your audition songs. 
You can download your songs [here].
Regards,
The Blenders",
            'delay'			=> 0,
            'created_at'        => Carbon::now(),
            'updated_at'        => Carbon::now(),
			],
            [
                'task_id'		=> 3,
                'subject'		=> 'Singer Completed Audition',
                'recipients'	=> 'role:2',
                'body'			=>
                    "Hi %%user.name%%,
Please congratulate %%singer.name%% for passing their audition!",
                'delay'			=> 0,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> 3,
                'subject'		=> 'Congratulations! You passed your audition',
                'recipients'	=> 'singer:1',
                'body'			=>
                    "Hi %%singer.name%%,
Congratulations on passing your audition. You are now eligible to become a full member of The Blenders. 
To complete your membership, please pay your fees ASAP. 
Regards,
The Blenders",
                'delay'			=> 0,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
		]);
    }
}
