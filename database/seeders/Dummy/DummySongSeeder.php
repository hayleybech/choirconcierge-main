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
use Illuminate\Support\Facades\Storage;

class DummySongSeeder extends Seeder
{
    public function run(): void
    {
        // Fetch all song statuses and categories
        $statuses = SongStatus::all();
        $categories = SongCategory::all();

        $this->insertRandomlyGeneratedSongs($statuses, $categories);
        $this->insertDemoSong($statuses);
    }

    private function insertRandomlyGeneratedSongs(Collection $statuses, Collection $categories): void
    {
        Song::factory()
            ->count(30)
            ->create()
            ->each(static function (Song $song) use ($statuses, $categories) {
                self::attachRandomStatus($song, $statuses);
                self::attachRandomCategories($song, $categories);

                // Generate random attachments
                Storage::disk('public')->makeDirectory('songs/' . $song->id);
                self::insertSampleMp3s($song);
                self::insertSamplePdfs($song);
            });
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

    private static function insertSampleMp3s(Song $song): void
    {
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
                $song_dir = Storage::disk('public')->path('songs/' . $song->id);

                $faker = Faker\Factory::create();
                $attachment->filepath = $faker->file($demo_dir, $song_dir, false);
                $attachment->type = 'learning-tracks';
                $attachment->save();
            });
    }

    private static function insertSamplePdfs(Song $song): void
    {
        $song
            ->attachments()
            ->saveMany(
                SongAttachment::factory()
                    ->count(1)
                    ->make(),
            )
            ->each(static function (SongAttachment $attachment) use ($song) {
                $demo_dir = Storage::disk('global-local')->path('sample/pdf');
                $song_dir = Storage::disk('public')->path('songs/' . $song->id);

                $faker = Faker\Factory::create();
                $attachment->filepath = $faker->file($demo_dir, $song_dir, false);
                $attachment->type = 'sheet-music';
                $attachment->save();
            });
    }

    /*
     * Insert a real song
     * My arrangement of Touch of Paradise
     */
    private function insertDemoSong(Collection $statuses): void
    {
        $description = "<p>This demo song showcases some of the best features of Choir Concierge. Here are some things to try:</p><ul><li><p>Hit \"Play\" on a learning track. Take note of the audio player down the bottom and how it hangs around as you browse other pages. You can get back by clicking the song title.</p></li><li><p>Now open the sheet music (mobile) or expand it to full screen (desktop). Take note that the audio player (if you've opened it) will stay open - it's so easy to use learning tracks and sheet music at the same time!</p></li><li><p>There's always a pitch pipe handy in the correct key: on the songs list next to the titles, on individual song pages at the top, and even in the PDF viewer. The PDF viewer also has a complete chromatic scale - just hit the button with the piano icon!</p></li><li><p>Try using it on a phone!</p></li></ul>";
        $song = Song::create([
            'title' => '1. Touch of Paradise (Demo Song)',
            'pitch_blown' => 5,
            'status' => $statuses->first()->id,
            'description' => $description,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $files = [
            [
                'file' => 'Touch of Paradise FULL e3.mp3',
                'type' => 'full-mix-demo',
            ],
            [
                'file' => 'Touch_of_Paradise v2-3.pdf',
                'type' => 'sheet-music',
            ],
            [
                'file' => 'Touch of Paradise ALTO e3.mp3',
                'type' => 'learning-tracks',
            ],
            [
                'file' => 'Touch of Paradise BASS e3.mp3',
                'type' => 'learning-tracks',
            ],
            [
                'file' => 'Touch of Paradise SOPR e3.mp3',
                'type' => 'learning-tracks',
            ],
            [
                'file' => 'Touch of Paradise TNRH e3.mp3',
                'type' => 'learning-tracks',
            ],
            [
                'file' => 'Touch of Paradise TNRL e3.mp3',
                'type' => 'learning-tracks',
            ],
        ];

        foreach ($files as $file) {
            $path = Storage::disk('global-local')->path('sample/top/' . $file['file']);

            if(! Storage::disk('global-local')->exists('sample/top/' . $file['file'])) {
                continue;
            }

            SongAttachment::create([
                'title' => '',
                'song_id' => $song->id,
                'file' => $path,
                'filepath' => $file['file'],
                'type' => $file['type'],
            ]);
        }
    }
}
