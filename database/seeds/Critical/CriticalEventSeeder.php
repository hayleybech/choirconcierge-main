<?php

use App\Models\EventType;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CriticalEventSeeder extends Seeder
{
    public function run(): void
    {
        // Insert stock event types
        DB::table('event_types')->insert([
            ['title' => 'Performance', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Rehearsal', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Social Event', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Other', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }

}