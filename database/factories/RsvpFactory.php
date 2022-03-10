<?php

namespace Database\Factories;

use App\Models\Rsvp;
use Illuminate\Database\Eloquent\Factories\Factory;

class RsvpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'response' => $this->faker->randomElement(['yes', 'no']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
