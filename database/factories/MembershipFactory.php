<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\SingerCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'singer_category_id' => SingerCategory::where('name', 'Members')->value('id'),
            'reason_for_joining' => $this->faker->sentence(),
            'referrer' => $this->faker->sentence(),
            'membership_details' => $this->faker->sentence(),
            'onboarding_enabled' => false,
            'joined_at' => $this->faker->dateTimeBetween(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
