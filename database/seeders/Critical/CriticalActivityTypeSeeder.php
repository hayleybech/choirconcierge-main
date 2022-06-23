<?php

namespace Database\Seeders\Critical;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriticalActivityTypeSeeder extends Seeder
{
    public function run(): void
    {
        // Not tenanted
        DB::table('activity_types')->insert([
            ['name' => 'Rehearsal'],
            ['name' => 'Warm-Ups'],
            ['name' => 'Workshop'],
            ['name' => 'Performance'],
            ['name' => 'Break'],
            ['name' => 'Speaking'],
            ['name' => 'Other'],
        ]);
    }
}
