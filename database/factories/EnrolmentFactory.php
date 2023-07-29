<?php

namespace Database\Factories;

use App\Models\Ensemble;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

class EnrolmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'membership_id' => Membership::factory(),
            'ensemble_id' => Ensemble::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
