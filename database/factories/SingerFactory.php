<?php

namespace Database\Factories;

use App\Models\Singer;
use App\Models\SingerCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SingerFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Singer::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'first_name' => $this->faker->firstName(),
			'last_name' => $this->faker->lastName(),
			'email' => $this->faker->safeEmail(),
			'user_id' => User::factory(),
			'singer_category_id' => SingerCategory::where('name', 'Members')->value('id'),
			'onboarding_enabled' => false,
			'joined_at' => $this->faker->dateTimeBetween(),
			'created_at' => now(),
			'updated_at' => now(),
		];
	}
}
