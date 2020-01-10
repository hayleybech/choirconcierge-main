<?php
/**
 * Created by PhpStorm.
 * User: hayde
 * Date: 11/11/2018
 * Time: 7:19 PM
 */

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SongAttachmentCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('song_attachment_categories')->delete();

        DB::table('song_attachment_categories')->insert([
            ['title' => 'Sheet Music'],
            ['title' => 'Full Mix (Demo)'],
            ['title' => 'Learning Tracks'],
            ['title' => 'Other'],
        ]);
    }
}