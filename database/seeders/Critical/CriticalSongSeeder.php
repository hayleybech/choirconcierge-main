<?php

namespace Database\Seeders\Critical;

use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\SongAttachmentCategory;
use App\Models\SongCategory;
use App\Models\SongStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CriticalSongSeeder extends Seeder
{
    public function run(): void
    {
        // Insert song statuses
        DB::table('song_statuses')->insert([
            [
                'tenant_id' => tenant('id'),
                'title' => 'Pending',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'title' => 'Learning',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'title' => 'Active',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'title' => 'Archived',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);

        // Insert song categories
        DB::table('song_categories')->insert([
            [
                'tenant_id' => tenant('id'),
                'title' => 'General',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'title' => 'Contest',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'title' => 'Polecats',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'title' => 'Christmas',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'tenant_id' => tenant('id'),
                'title' => 'Special Events',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ]);
    }
}
