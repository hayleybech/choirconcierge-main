<?php

namespace Database\Factories;

use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserGroupFactory extends Factory
{
    /** @var string */
    protected $model = UserGroup::class;

    public function definition(): array
    {
        return [
            'title'         => $this->faker->sentence,
            'slug'          => $this->faker->unique()->slug,
            'list_type'     => $this->faker->randomElement(['public', 'chat', 'distribution']),
	        'created_at'    => now(),
	        'updated_at'    => now(),
        ];
    }
}
