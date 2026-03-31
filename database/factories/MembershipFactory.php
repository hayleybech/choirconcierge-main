<?php

namespace Database\Factories;

use App\Models\Membership;
use App\Models\SingerStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MembershipFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'reason_for_joining' => $this->faker->sentence(),
            'referrer' => $this->faker->sentence(),
            'membership_details' => $this->faker->sentence(),
            'onboarding_enabled' => false,
            'joined_at' => $this->faker->dateTimeBetween(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Membership $membership) {
            $statusId = SingerStatus::where('name', 'Members')->value('id') ?? SingerStatus::factory()->create(['name' => 'Members'])->id;
            $membership->statuses()->attach($statusId);
        });
    }
}
