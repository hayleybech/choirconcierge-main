<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\Singer;
use App\Models\Profile;
use App\Models\Placement;
use App\Models\Task;
use App\Models\SingerCategory;

class DripSingersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        // Import Prep

        // Fetch all possible Tasks to attach to singer
        $tasks = Task::all();

        // Loop through singer rows in CSV
        foreach ($rows as $row)
        {

            // Create the singer
            $singer = Singer::create([
                'email' => $row['email'],
                'name'  => $row['name'],
            ]);

            // Create the Member Profile
            $profile = new Profile([
                'dob'                   => ($row['dob']) ? date_create($row['dob']) : null,
                'phone'                 => '0'.$row['phone'],
                'ice_name'              => $row['ice_name'],
                'ice_phone'             => '0'.$row['ice_phone'],
                'reason_for_joining'    => $row['mp_looking_for'],
                'referrer'              => $row['mp_hear_about'],
                'profession'            => '',
                'skills'                => $row['mp_other_skills'],
            ]);
            $singer->profile()->save($profile);

            // Create the Voice Placement
            $placement = new Placement([
                'experience'            => $row['vp_singing_experience'],
                'instruments'           => $row['vp_instruments'],
                'skill_pitch'           => $row['vp_pitch'],
                'skill_harmony'         => $row['vp_harmony'],
                'skill_performance'     => $row['vp_performance'],
                'skill_sightreading'    => $row['vp_sight_reading'],
                'voice_tone'            => $row['vp_voice_tone'],
                'voice_part'            => $row['voice_part'],
            ]);
            $singer->placement()->save($placement);

            // Get all Drip tags to check for completed tasks and categories
            $tags = explode( ',', $row['tags']);

            // Attach all tasks to singer and mark completed
            foreach($tasks as $task)
            {
                $completed = false;

                // Mark task completed if row has relevant tag
                if(
                       ( $task->name === 'Member Profile' && in_array('Member Profile Completed', $tags) )
                    || ( $task->name === 'Voice Placement' && in_array('Voice Placement Completed', $tags) )
                    || ( $task->name === 'Pass Audition' && in_array('Passed Vocal Assessment', $tags) )
                    || ( $task->name === 'Pay Fees' && in_array('Membership Fees Paid', $tags) )
                    || ( $task->name === 'Provide Uniform' && in_array('Uniform Provided', $tags) )
                    || ( $task->name === 'Create Account' && in_array('Account Created', $tags) )
                )
                {
                    $completed = true;
                }

                $task->singers()->attach($singer, ['completed' => $completed]);
            }

            // Attach ONE category to singer
            if( in_array('Category - Active Member', $tags ) )
            {
                $cat = SingerCategory::where('name', 'Members')->first();
            }
            elseif ( in_array('Category - Archived', $tags) )
            {
                $cat = SingerCategory::where('name', 'Archived Prospects')->first();
            }
            else
            {
                $cat = SingerCategory::where('name', 'Prospects')->first();
            }
            $singer->category()->associate($cat);

            $singer->save();
        }
    }
}