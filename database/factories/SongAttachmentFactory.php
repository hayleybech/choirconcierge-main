<?php

namespace Database\Factories;

use App\Models\SongAttachment;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongAttachmentFactory extends Factory
{
    /** @var string */
    protected $model = SongAttachment::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(2, true),
            'filepath' => '---',    // Set in seeder, this temporarily circumvents non nullable column
        ];
    }
}
