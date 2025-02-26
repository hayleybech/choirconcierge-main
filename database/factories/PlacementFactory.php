<?php

namespace Database\Factories;

use App\Models\Placement;
use Illuminate\Database\Eloquent\Factories\Factory;

class PlacementFactory extends Factory
{
    public function definition(): array
    {
        return [
            'experience' => $this->faker->sentence(),
            'instruments' => $this->faker->sentence(),
            'skill_pitch' => $this->faker->numberBetween(1, 5),
            'skill_harmony' => $this->faker->numberBetween(1, 5),
            'skill_performance' => $this->faker->numberBetween(1, 5),
            'skill_sightreading' => $this->faker->numberBetween(1, 5),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
