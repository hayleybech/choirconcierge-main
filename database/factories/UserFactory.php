<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'password' => bcrypt('password'),
            'remember_token' => Str::random(10),
            'dob' => $this->faker->dateTimeBetween('-100 years', '-5 years'),
            'phone' => $this->faker->phoneNumber(),
            'ice_name' => $this->faker->name(),
            'ice_phone' => $this->faker->phoneNumber(),
            'address_street_1' => $this->faker->streetAddress(),
            'address_street_2' => $this->faker->secondaryAddress(),
            'address_suburb' => $this->faker->city(),
            'address_state' => $this->faker->stateAbbr(),
            'address_postcode' => $this->faker->numerify('####'),
            'profession' => $this->faker->sentence(),
            'skills' => $this->faker->sentence(),
            'height' => $this->faker->randomFloat(2, 0, 300),
            'bha_id' => $this->faker->numerify('####'),
        ];
    }
}
