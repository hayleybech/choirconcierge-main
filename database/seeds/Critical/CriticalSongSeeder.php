<?php

use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\SongAttachmentCategory;
use App\Models\SongCategory;
use App\Models\SongStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class CriticalSongSeeder extends Seeder
{
    public function run(): void
    {
        // Insert song statuses
        DB::table('song_statuses')->insert([
            ['title' => 'Pending', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Learning', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Archived', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // Insert song categories
        DB::table('song_categories')->insert([
            ['title' => 'General', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Contest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Polecats', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Christmas', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Special Events', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // Insert song attachment categories
        DB::table('song_attachment_categories')->insert([
            ['title' => 'Sheet Music'],
            ['title' => 'Full Mix (Demo)'],
            ['title' => 'Learning Tracks'],
            ['title' => 'Other'],
        ]);
    }
}