<?php

namespace Database\Factories;

use App\Models\Attendance;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttendanceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attendance::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
	        'response'      => $this->faker->randomElement(['present', 'absent', 'absent_apology']),
	        'absent_reason' => $this->faker->optional(0.3)->sentence(),
	        'created_at'    => now(),
	        'updated_at'    => now(),
        ];
    }
}
