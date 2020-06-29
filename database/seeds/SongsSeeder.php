<?php

use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\SongAttachmentCategory;
use App\Models\SongCategory;
use App\Models\SongStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SongsSeeder extends Seeder
{
    public function run(): void
    {
        /*
         * STEP 0 - Clear
         */
        DB::table('song_attachments')->delete();
        DB::table('song_attachment_categories')->delete();
        DB::table('songs')->delete();
        DB::table('songs_song_categories')->delete();
        DB::table('song_statuses')->delete();
        DB::table('song_categories')->delete();

        /*
         * STEP 1 - Insert initial real data
         */

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

        /*
         * STEP 2 - Insert and attach dummy songs
         */

        // Fetch all song statuses and categories
        $statuses = SongStatus::all();
        $categories = SongCategory::all();
        $attachment_categories = SongAttachmentCategory::all();

        // Generate random songs
        factory(Song::class, 30)->create()->each(static function(Song $song) use ($statuses, $categories, $attachment_categories) {
            SongsSeeder::attachRandomStatus($song, $statuses);
            SongsSeeder::attachRandomCategories($song, $categories);

            // Generate random attachments
            Storage::disk('public')->makeDirectory( 'songs/'.$song->id);

            // Sample mp3s
            $song->attachments()->saveMany( factory( SongAttachment::class, 4 )->make() )
                ->each(static function(SongAttachment $attachment) use ($song, $attachment_categories) {

                    // Copy random sample files
                    // Computer-generated music from https://www.fakemusicgenerator.com/
                    $demo_dir = storage_path('app/sample/mp3');
                    $song_dir = storage_path('app/public/songs/'.$song->id );
                    $faker = Faker\Factory::create();
                    $attachment->filepath = $faker->file( $demo_dir, $song_dir, false );

                    // Attach to learning tracks category
                    $category = SongAttachmentCategory::where('title', '=', 'Learning Tracks')->first();
                    $attachment->category()->associate($category);
                    $attachment->save();
                });

            // Sample PDFs
            Storage::disk('public')->makeDirectory( 'songs/'.$song->id);
            $song->attachments()->saveMany( factory( SongAttachment::class, 1 )->make() )
                ->each(static function(SongAttachment $attachment) use ($song, $attachment_categories) {

                    $demo_dir = storage_path('app/sample/pdf');
                    $song_dir = storage_path('app/public/songs/'.$song->id );
                    $faker = Faker\Factory::create();
                    $attachment->filepath = $faker->file( $demo_dir, $song_dir, false );

                    // Attach to sheet music category
                    $category = SongAttachmentCategory::where('title', '=', 'Sheet Music')->first();
                    $attachment->category()->associate($category);
                    $attachment->save();
                });
        });

        /*
         * ALSO - Insert a real song
         * My arrangement of Touch of Paradise
         */
        DB::table('songs')->insert([
            ['title' => 'Touch of Paradise', 'pitch_blown' => 5, 'status_id' => $statuses->first()->id, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
        ]);
    }

    /**
     * @param Song $song
     * @param Collection $statuses
     * @throws Exception
     */
    public static function attachRandomStatus(Song $song, Collection $statuses): void
    {
        $status = $statuses->random(1)->first();
        $song->status()->associate($status);
        $song->save();
    }

    /**
     * @param Song $song
     * @param Collection $categories
     * @throws Exception
     */
    public static function attachRandomCategories(Song $song, Collection $categories): void
    {
        $qty = random_int(0, 3 ); // How many categories should this song have?
        $category = $categories->random($qty);
        $song->categories()->attach($category);
        $song->save();
    }
}