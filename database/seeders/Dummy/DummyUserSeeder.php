<?php

namespace Database\Seeders\Dummy;

use App\Models\SingerCategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use App\Models\Singer;
use Carbon\Carbon;
use Faker\Factory as Faker;

class DummyUserSeeder extends Seeder
{
	public function run(): void
	{
		$singer_categories = SingerCategory::all();

		// Add dummy users - no roles
		User::factory()
			->count(30)
			->create()
			->each(static function (User $user) use ($singer_categories) {
				$faker = Faker::create();

				// Create matching singer
				$name = explode(' ', $user->name);
				$user->singer()->create([
					'first_name' => $name[0],
					'last_name' => $name[1],
					'email' => $user->email,
					'onboarding_enabled' => $faker->boolean(30),
				]);

				// Attach random singer category
				DummyUserSeeder::attachRandomSingerCategory($user->singer, $singer_categories);

				// Generate profile and placement for singer
				// @todo Seed singer profile and voice placement

				// Generate tasks
				// @todo Generate tasks for dummy singers
			});
	}

	public static function attachRandomSingerCategory(Singer $singer, Collection $categories): void
	{
		$category = $categories->random(1)->first();
		$singer->category()->associate($category);
		$singer->save();
	}
}
