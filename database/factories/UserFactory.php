<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /** @var string */
    protected $model = User::class;

    // All faked users have "password" for their password, so only bcrypt() once
    protected static string $password;

    public function definition(): array
    {
        return [
            'name' => $this->faker->firstName . ' ' . $this->faker->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'password' => self::$password ?: self::$password = bcrypt('password'),
            'remember_token' => Str::random(10),
        ];
    }
}
