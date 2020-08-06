<?php

use App\Models\Task;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DummyNotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profile_reminder_body = <<<EOT
Hi %%user.name%%,

A prospective member, %%singer.name%%, has completed a voice placement. Please complete the Member Profile to provide basic information and contact details for the singer.

[Click here to start the Member Profile.](http://choirconcierge.test/singers/3/profile/create?task=1)

Cheers,

Choir Concierge
EOT;

        $placement_reminder_body = <<<EOT
Hi %%user.name%%,

A prospective member, %%singer.name%%, is ready for voice placement. 

Click here to start the voice placement.

Cheers,

Choir Concierge
EOT;


        $details_completed_body = <<<EOT
Hi %%user.name%%,

A prospective member, %%singer.name%%, has completed a Voice Placement. The singer is in the %%singer.section%% section.

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

Membership Team - The Blenders

__Your Journey__
- _Complete a Voice Placement - Done_
- __Pass an Audition - You are here__
- Pay your fees
- Get fitted for a uniform
- Download our entire repertoire and start singing!
EOT;
        $audition_due_singer_body = <<<EOT
Hi %%singer.name%%,

We hope you've had a fantastic few weeks at The Blenders. It's now time for your Vocal Assessment! After your initial four weeks with us, you must be assessed by one of our friendly music team.

Don't worry if you're not feeling ready! It's a great chance to get some individual feedback and guidance, plus we'll tell you exactly where to focus your efforts to pass as quickly as possible. 

At your next rehearsal, speak with your section leader. They will assess you before or after rehearsal, or during the break.

Good luck!

Membership Team - The Blenders
EOT;

        $audition_due_team_body = <<<EOT
Hi %%user.name%%,

%%singer.name%% is due for vocal assessment.

Cheers,

Choir Concierge
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

[__Download the Songs__](https://www.dropbox.com/sh/m2uzdaq1sfpg6a3/AABip8SRfM9A0_sHC6WXzeiBa?dl=0')

Thanks

Membership Team - The Blenders

__Your Journey__

- _Complete a Voice Placement - Done_
- _Pass an Audition - Done_
- __Pay your fees - You are here__
- Get fitted for a uniform
- Download our entire repertoire and start singing!
EOT;

        $uniform_request_body = <<<EOT
Hi %%user.name%%,

A new member, %%singer.name%%, has paid their fees.

Uniforms Team: Please provide a uniform for the singer then mark the task complete.

Cheers,

Choir Concierge
EOT;

        $membership_welcome_body = <<<EOT
Hi %%user.name%%,

A new member, %%singer.name%%, has paid their fees.

Membership Team: Please provide a name badge and a Groupanizer account, and organise the singer's BHA registration. Make sure the task is marked complete. 

Singer's email: %%singer.email%%
Voice Part: %%singer.section%%

Cheers,

Choir Concierge
EOT;

        $director_welcome_body = <<<EOT
Hi %%user.name%%,

A new member, %%singer.name%%, has paid their fees.

Director: Please invite the singer to the Facebook group chat, and add them to the bulk list on your phone.

Singer's email: %%singer.email%%
Singer's phone: %%singer.phone%%
Voice Part: %%singer.section%%

Cheers,

Choir Concierge
EOT;



		$tasks = Task::all();
		// Create some dummy templates
		DB::table('notification_templates')->insert([
            /*[
                'task_id'		=> $tasks->firstWhere('name', '=', 'Member Profile')->id,
                'subject'		=> 'Singer ready for Voice Placement',
                'recipients'	=> 'role:2',
                'body'			=> $placement_reminder_body,
                'delay'			=> '3 hours',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Voice Placement')->id,
                'subject'		=> 'Singer ready for Member Profile',
                'recipients'	=> 'role:3',
                'body'			=> $profile_reminder_body,
                'delay'			=> '3 hours',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],*/
			[
                'task_id'		=> $tasks->firstWhere('name', '=', 'Voice Placement')->id,
                'subject'		=> 'Singer completed their details',
                'recipients'	=> 'role:2',
                'body'			=> $details_completed_body,
                'delay'			=> '4 hours',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
			],
			[
                'task_id'		=> $tasks->firstWhere('name', '=', 'Voice Placement')->id,
                'subject'		=> 'Welcome to The Blenders!',
                'recipients'	=> 'singer:0',
                'body'			=> $welcome_body,
                'delay'			=> '4 hours',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
			],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Voice Placement')->id,
                'subject'		=> 'Time for your Vocal Assessment!',
                'recipients'	=> 'singer:0',
                'body'			=> $audition_due_singer_body,
                'delay'			=> '28 days',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Voice Placement')->id,
                'subject'		=> 'A singer is due for Vocal Assessment',
                'recipients'	=> 'role:2',
                'body'			=> $audition_due_team_body,
                'delay'			=> '28 days',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Pass Audition')->id,
                'subject'		=> 'Congratulations! You passed your audition',
                'recipients'	=> 'singer:0',
                'body'			=> $audition_congrats_body,
                'delay'			=> '1 second',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Pass Audition')->id,
                'subject'		=> 'Please invoice new member',
                'recipients'	=> 'role:4',
                'body'			=> $audition_completed_body,
                'delay'			=> '1 second',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Pay Fees')->id,
                'subject'		=> 'Please provide uniform to new member',
                'recipients'	=> 'role:5',
                'body'			=> $uniform_request_body,
                'delay'			=> '1 second',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Pay Fees')->id,
                'subject'		=> 'Please welcome new member',
                'recipients'	=> 'role:3',
                'body'			=> $membership_welcome_body,
                'delay'			=> '1 second',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
            [
                'task_id'		=> $tasks->firstWhere('name', '=', 'Pay Fees')->id,
                'subject'		=> 'Please welcome new member',
                'recipients'	=> 'user:1',
                'body'			=> $director_welcome_body,
                'delay'			=> '1 second',
                'created_at'        => Carbon::now(),
                'updated_at'        => Carbon::now(),
            ],
		]);
    }
}
