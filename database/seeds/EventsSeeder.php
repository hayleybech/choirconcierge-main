<?php
/**
 * Created by PhpStorm.
 * User: hayde
 * Date: 11/11/2018
 * Time: 7:19 PM
 */

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class EventsSeeder extends Seeder
{
    public function run()
    {
        DB::table('events')->delete();
        DB::table('event_types')->delete();

        DB::table('event_types')->insert([
            ['title' => 'Performance', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Rehearsal', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Social Event', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Other', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);
    }
}