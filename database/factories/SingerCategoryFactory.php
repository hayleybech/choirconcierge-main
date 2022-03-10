<?php

namespace Database\Factories;

use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class SingerCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->word(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
