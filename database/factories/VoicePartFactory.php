<?php

namespace Database\Factories;

use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoicePartFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => $this->faker->word(),
            'colour' => $this->faker->hexColor(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
