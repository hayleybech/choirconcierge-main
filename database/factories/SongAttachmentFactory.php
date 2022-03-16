<?php

namespace Database\Factories;

use App\Models\SongAttachment;
use App\Models\SongAttachmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class SongAttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => '',
            'file' => UploadedFile::fake()->create('random.mp3'),
            'category_id' => SongAttachmentCategory::inRandomOrder()->value('id'),
        ];
    }
}
