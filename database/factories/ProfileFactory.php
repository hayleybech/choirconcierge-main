<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
	        'dob'                   => $this->faker->dateTimeBetween('-100 years', '-5 years'),
	        'phone'                 => $this->faker->phoneNumber(),
	        'ice_name'              => $this->faker->name(),
	        'ice_phone'             => $this->faker->phoneNumber(),
	        'address_street_1'      => $this->faker->streetAddress(),
	        'address_street_2'      => $this->faker->secondaryAddress(),
	        'address_suburb'        => $this->faker->city(),
	        'address_state'         => $this->faker->stateAbbr(),
	        'address_postcode'      => $this->faker->numerify('####'),
	        'reason_for_joining'    => $this->faker->sentence(),
	        'referrer'              => $this->faker->sentence(),
	        'profession'            => $this->faker->sentence(),
	        'skills'                => $this->faker->sentence(),
	        'height'                => $this->faker->randomFloat(2, 0, 300),
	        'membership_details'    => $this->faker->sentence(),
	        'created_at'            => now(),
	        'updated_at'            => now(),
        ];
    }
}
