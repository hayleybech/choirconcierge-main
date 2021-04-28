<?php

namespace Database\Factories;

use App\Models\SongAttachment;
use App\Models\SongAttachmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class SongAttachmentFactory extends Factory
{
    /** @var string */
    protected $model = SongAttachment::class;

    public function definition(): array
    {
        return [
        	'song_id'       => '#', // overridden later, added here to ensure it's added before file, otherwise tests would fail.
	        'title'         => '',
	        'file'          => UploadedFile::fake()->create('random.mp3'),
	        'category_id'   => SongAttachmentCategory::inRandomOrder()->value('id'),
        ];
    }
}
