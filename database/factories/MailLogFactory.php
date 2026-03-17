<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MailLog>
 */
class MailLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'uid' => $this->faker->unique()->uuid(),
            'from' => $this->faker->safeEmail(),
            'to' => $this->faker->safeEmail(),
            'subject' => $this->faker->sentence(),
            'body' => $this->faker->paragraph(),
            'received_at' => now(),
        ];
    }
}
