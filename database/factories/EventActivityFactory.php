<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EventActivityFactory extends Factory
{
    public function definition(): array
    {
        return [
            'order' => $this->faker->unique()->randomNumber(),
            'notes' => $this->faker->optional(0.2)->sentence(),
            'duration' => $this->faker->optional(0.7)->numberBetween(3, 60),
        ];
    }
}
