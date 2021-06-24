<?php

namespace Database\Factories;

use App\Models\RiserStack;
use Illuminate\Database\Eloquent\Factories\Factory;

class RiserStackFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = RiserStack::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'title' => $this->faker->sentence(),
			'rows' => $this->faker->numberBetween(2, 5),
			'columns' => $this->faker->numberBetween(1, 8),
			'front_row_length' => $this->faker->numberBetween(1, 10),
			'front_row_on_floor' => $this->faker->boolean(),
			'created_at' => now(),
			'updated_at' => now(),
		];
	}
}
