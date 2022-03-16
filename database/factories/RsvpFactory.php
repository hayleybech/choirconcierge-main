<?php

namespace Database\Factories;

use App\Models\Rsvp;
use Illuminate\Database\Eloquent\Factories\Factory;

class RsvpFactory extends Factory
{
    public function definition(): array
    {
        return [
            'response' => $this->faker->randomElement(['yes', 'no']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
