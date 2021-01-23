<?php

namespace Database\Factories;

use App\Models\Song;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongFactory extends Factory
{
    /** @var string */
    protected $model = Song::class;

    public function definition(): array
    {
        $pitch = array_rand(Song::getAllPitches());

        return [
            'title' => $this->faker->sentence(6, true),
            'pitch_blown' => $pitch,
        ];
    }
}
