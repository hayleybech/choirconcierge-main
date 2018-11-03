<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Singer;
use App\Profile;
use App\Placement;
use App\Task;

class DripSingersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Create the singer
            $singer = Singer::create([
                'email' => $row['email'],
                'name'  => $row['name'],
            ]);

            // Create the Member Profile
            $profile = new Profile([
                'dob'                   => ($row['dob']) ? date_create($row['dob']) : null,
                'phone'                 => $row['phone'],
                'ice_name'              => $row['ice_name'],
                'ice_phone'             => $row['ice_phone'],
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

            // Create the Singer's Tasks
            $tasks = Task::all();

            foreach($tasks as $task){
                $task->singers()->attach($singer);
            }
        }
    }
}