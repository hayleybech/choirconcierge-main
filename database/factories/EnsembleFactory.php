<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EnsembleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence,
            'tenant_id' => tenant('id'),
        ];
    }
}
