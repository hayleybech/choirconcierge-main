<?php

namespace Database\Factories;

use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SingerCategoryFactory extends Factory
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
