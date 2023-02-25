<?php

namespace Database\Seeders\Dummy;

use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\SongCategory;
use App\Models\SongStatus;
use Carbon\Carbon;
use Exception;
use Faker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DummySongSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all song statuses and categories
        $statuses = SongStatus::all();
        $categories = SongCategory::all();

        // Generate random songs
        Song::factory()
            ->count(30)
            ->create()
            ->each(static function (Song $song) use ($statuses, $categories) {
                self::attachRandomStatus($song, $statuses);
                self::attachRandomCategories($song, $categories);

                // Generate random attachments
                Storage::disk('public')->makeDirectory('songs/'.$song->id);

                // Sample mp3s
                $song
                    ->attachments()
                    ->saveMany(
                        SongAttachment::factory()
                            ->count(4)
                            ->make(),
                    )
                    ->each(static function (SongAttachment $attachment) use ($song) {
                        // Copy random sample files
                        // Computer-generated music from https://www.fakemusicgenerator.com/
                        $demo_dir = Storage::disk('global-local')->path('sample/mp3');
                        $song_dir = Storage::disk('public')->path('songs/'.$song->id);
                        $faker = Faker\Factory::create();
                        $attachment->filepath = $faker->file($demo_dir, $song_dir, false);
                        $attachment->type = 'learning-tracks';
                    });

                // Sample PDFs
                Storage::disk('public')->makeDirectory('songs/'.$song->id);
                $song
                    ->attachments()
                    ->saveMany(
                        SongAttachment::factory()
                            ->count(1)
                            ->make(),
                    )
                    ->each(static function (SongAttachment $attachment) use ($song) {
                        $demo_dir = Storage::disk('global-local')->path('sample/mp3');
                        $song_dir = Storage::disk('public')->path('songs/'.$song->id);
                        $faker = Faker\Factory::create();
                        $attachment->filepath = $faker->file($demo_dir, $song_dir, false);
                        $attachment->type = 'sheet-music';
                    });
            });

        /*
         * ALSO - Insert a real song
         * My arrangement of Touch of Paradise
         */
        DB::table('songs')->insert([
            [
                'tenant_id' => tenant('id'),
                'title' => 'Touch of Paradise',
                'pitch_blown' => 5,
                'status_id' => $statuses->first()->id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
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
        $qty = random_int(0, 3); // How many categories should this song have?
        $category = $categories->random($qty);
        $song->categories()->attach($category);
        $song->save();
    }
}
