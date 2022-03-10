<?php

namespace Database\Factories;

use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoicePartFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->word(),
            'colour' => $this->faker->hexColor(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
