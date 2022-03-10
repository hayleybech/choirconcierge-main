<?php

namespace Database\Factories;

use App\Models\RiserStack;
use Illuminate\Database\Eloquent\Factories\Factory;

class RiserStackFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'rows' => $this->faker->numberBetween(2, 5),
            'columns' => $this->faker->numberBetween(1, 8),
            'front_row_length' => $this->faker->numberBetween(1, 10),
            'front_row_on_floor' => $this->faker->boolean(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
