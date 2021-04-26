<?php

namespace Database\Factories;

use App\Models\Folder;
use Illuminate\Database\Eloquent\Factories\Factory;

class FolderFactory extends Factory
{
    /** @var string */
    protected $model = Folder::class;

    public function definition(): array
    {
        return [
            'title'         => $this->faker->sentence,
	        'created_at'    => now(),
	        'updated_at'    => now(),
        ];
    }
}