<?php

namespace Database\Factories;

use App\Models\ForumCategory;
use App\Models\Membership;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ForumTopic>
 */
class ForumTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'forum_category_id' => ForumCategory::factory(),
            'membership_id' => Membership::factory(),
            'title' => fake()->sentence(4),
            'body' => fake()->paragraph(),
            'is_locked' => false,
            'is_pinned' => false,
        ];
    }
}
