<?php

namespace Database\Factories;

use App\Models\Role;
use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->sentence(3),
            'role_id' => Role::where('name', 'Music Team')->value('id'),
            'type' => 'manual',
            'route' => 'task.complete',
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
