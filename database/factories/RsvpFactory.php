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
            'details' => $this->faker->boolean(30)
                ? $this->faker->randomElement([
                    'Running a few minutes late',
                    'I may need to leave early',
                    'Looking forward to it',
                    'I can carpool if needed',
                    'Still confirming childcare',
                ])
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
