<?php

namespace Database\Factories;

use App\Models\SingerStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class SingerStatusFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
