<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class SongAttachmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => '',
            'file' => UploadedFile::fake()->create('random.mp3'),
            'type' => $this->faker->randomElement(['sheet-music', 'learning-tracks', 'full-mix-demo']),
        ];
    }
}
