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

        $profile_completed_body = <<<EOT
Hi %%user.name%%,
A prospective member, %%singer.name%%, has completed their Member Profile and their Voice Placement. The singer is in the %%singer.section%% section.

The member has received their audition songs. Section Leader, please keep in touch every week. 

Cheers,
Choir Concierge
EOT;


        $welcome_body = <<<EOT
Hi %%singer.name%%,
Welcome to the Blenders!

Thanks for your interest in joining. Our unique onboarding system, Choir Concierge, will email you with updates as you progress on your journey to becoming a member.

__Getting ready for your audition__
__[Here's the link to our audition songs.](https://theblenders.com.au/join-us/#audition-songs)__ Download the files you need, and start practising. You'll have up to four weeks to prepare, and your section leader will be in touch each week to see how you're going.

Good luck!
Membership Team
The Blenders

__Your Journey__
- _Complete a Voice Placement - Done_
- __Pass an Audition - You are here__
- Pay your fees
- Get fitted for a uniform
- Download our entire repertoire and start singing!
EOT;

        $audition_completed_body = <<<EOT
Hi %%user.name%%,
A new member, %%singer.name%%, has passed their audition. Please invoice them for their registration fees. Ensure you inform us when the fees have been paid. 

Here are the subscriber's details:
Email: %%singer.email%%
DOB: %%singer.dob%%
Age: %%singer.age%%

Cheers,
Choir Concierge
EOT;

        $audition_congrats_body = <<<EOT
Hi %%singer.name%%,
Congratulations on passing your audition! You are now eligible to become a full member of The Blenders! Below, you'll find a link to The Blenders' core repertoire, which you can start learning straight away. 

As a member, you're expected to learn a few core songs for special moments:
- "__Happy Birthday__" is a song you can expect to sing to someone in the choir almost every week!
- The "__Let's Get Together Again / Keep the Whole World Singing__" medley is the "farewell" song of the Barbershop singing community, used at the end of every rehearsal or event.

The next step is to pay your registration fees and get fitted for a uniform. Our treasurer will send you an invoice in the next few days and you'll hear from our Uniforms officer soon, too.

Once you've paid your fees, you'll gain access to our members' website, __Groupanizer__, and you can start downloading and learning songs from our complete repertoire. Exciting!

[Download the Songs](https://www.dropbox.com/sh/m2uzdaq1sfpg6a3/AABip8SRfM9A0_sHC6WXzeiBa?dl=0')

Thanks
The Blenders Membership Team

__Your Journey__

- _Complete a Voice Placement - Done_
- _Pass an Audition - Done_
- __Pay your fees - You are here__
- Get fitted for a uniform
- Download our entire repertoire and start singing!
EOT;


		
		// Create some dummy templates
		DB::table('notification_templates')->insert([
			[
                'task_id'		=> 1,
                'subject'		=> 'Singer Completed Member Profile',
                'recipients'	=> 'user:1',
                'body'			=> $profile_completed_body,
                'delay'			=> 0,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
			],
			[
                'task_id'		=> 1,
                'subject'		=> 'Welcome to The Blenders!',
                'recipients'	=> 'singer:1',
                'body'			=> $welcome_body,
                'delay'			=> 0,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
			],
            [
                'task_id'		=> 3,
                'subject'		=> 'Please invoice new member',
                'recipients'	=> 'role:4',
                'body'			=> $audition_completed_body,
                'delay'			=> 0,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> 3,
                'subject'		=> 'Congratulations! You passed your audition',
                'recipients'	=> 'singer:1',
                'body'			=> $audition_congrats_body,
                'delay'			=> 0,
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
		]);
    }
}
