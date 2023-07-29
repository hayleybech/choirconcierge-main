<?php

namespace Database\Seeders\Dummy;

use App\Models\Membership;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

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
                $member = $user->memberships()->create([
                    'onboarding_enabled' => $faker->boolean(30),
                ]);

                // The ResetDemoSite job also tries to add an ensemble,
                // but this file runs before the rest of the ResetDemoSite job runs.
                if(tenant()->ensembles->count() === 0) {
                    tenant()->ensembles()->create(['name' => tenant('name')]);
                }
                // Create enrolment
                tenant()->ensembles()?->first()->enrolments()->create([
                    'membership_id' => $member->id,
                    'voice_part_id' => VoicePart::query()->inRandomOrder()->first()->id,
                ]);

                // Attach random singer category
                self::attachRandomSingerCategory($member, $singer_categories);

                // Generate placement for singer
                // @todo Seed voice placement

                // Generate tasks
                // @todo Generate tasks for dummy singers
            });
    }

    public static function attachRandomSingerCategory(Membership $member, Collection $categories): void
    {
        $category = $categories->random(1)->first();
        $member->category()->associate($category);
        $member->save();
    }
}
