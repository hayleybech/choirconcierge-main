<?php
/**
 * Created by PhpStorm.
 * User: hayde
 * Date: 11/11/2018
 * Time: 7:19 PM
 */

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SongsSeeder extends Seeder
{
    public function run()
    {
        DB::table('songs_song_categories')->delete();
        DB::table('songs')->delete();
        DB::table('song_categories')->delete();
        DB::table('song_statuses')->delete();

        DB::table('song_statuses')->insert([
            ['title' => 'Pending', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Learning', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Active', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Archived', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        DB::table('song_categories')->insert([
            ['title' => 'General', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Contest', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Polecats', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Christmas', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'Special Events', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);

        // @todo: move dummy seeder to separate seeder
        /*DB::table('songs')->insert([
            ['title' => 'Touch of Paradise', 'pitch_blown' => 'D', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
            ['title' => 'We Will Rock You', 'pitch_blown' => 'C', 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()],
        ]);*/
    }
}