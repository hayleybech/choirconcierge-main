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
            ['tenant_id' => tenant('id'), 'title' => 'Performance', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['tenant_id' => tenant('id'), 'title' => 'Rehearsal', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['tenant_id' => tenant('id'), 'title' => 'Social Event', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['tenant_id' => tenant('id'), 'title' => 'Other', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }

}