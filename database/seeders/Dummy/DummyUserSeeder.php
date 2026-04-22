<?php

namespace Database\Seeders\Dummy;

use App\Models\CustomField;
use App\Models\Membership;
use App\Models\Role;
use App\Enums\SingerStatus;
use App\Models\User;
use App\Models\VoicePart;
use Faker\Factory as Faker;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        $singer_statuses = SingerStatus::cases();

        // The ResetDemoSite job also tries to add an ensemble,
        // but this file runs before the rest of the ResetDemoSite job runs.
        if (tenant()->ensembles->count() === 0) {
            tenant()->ensembles()->firstOrCreate(['name' => 'Hypothetical Harmony']);
        }

        $custom_fields = CustomField::factory()->count(10)->create();
        $faker = Faker::create();

        $voice_parts = VoicePart::pluck('id');

        $userRole = Role::query()->firstWhere(['name' => 'User']);

        // Add dummy users
        User::factory()
            ->count(30)
            ->create()
            ->each(static function (User $user) use ($userRole, $voice_parts, $singer_statuses, $faker, $custom_fields) {

                // Create matching singer
                $member = Membership::factory()
                    ->for($user)
                    ->hasAttached($custom_fields, fn() => ['value' => $faker->words(3, true)])
                    ->hasAttached($userRole)
                    ->state([
                        'onboarding_enabled' => $faker->boolean(30),
                    ])
                    ->create();

                // Create enrolment
                tenant()->ensembles()?->first()->enrolments()->create([
                    'membership_id' => $member->id,
                    'voice_part_id' => $voice_parts->random(),
                ]);

                // Attach random singer status
                self::attachRandomSingerStatus($member, $singer_statuses);

                // Generate placement for singer
                // @todo Seed voice placement

                // Generate tasks
                // @todo Generate tasks for dummy singers
            });
    }

    public static function attachRandomSingerStatus(Membership $member, array $statuses): void
    {
        $status = $statuses[array_rand($statuses)];
        $member->statuses()->create(['status' => $status->value]);
    }
}
