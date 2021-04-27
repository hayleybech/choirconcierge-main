<?php

namespace Database\Factories;

use App\Models\Song;
use App\Models\SongStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class SongFactory extends Factory
{
    /** @var string */
    protected $model = Song::class;

    public function definition(): array
    {
        return [
            'title'         => $this->faker->sentence(6, true),
            'pitch_blown'   => $this->faker->numberBetween(0, count(Song::getAllPitches())),
	        'status_id'     => SongStatus::where('title', 'Active')->value('id'),
	        'created_at'    => now(),
	        'updated_at'    => now(),
        ];
    }
}
