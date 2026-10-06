<?php

namespace Database\Seeders\Dummy;

use App\Models\CustomField;
use App\Models\Membership;
use App\Models\Role;
use App\Enums\SingerStatus;
use App\Models\User;
use App\Models\VoicePart;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Faker\Generator;
use Illuminate\Database\Seeder;

class DummyUserSeeder extends Seeder
{
    public function run(): void
    {
        // The ResetDemoSite job also tries to add an ensemble,
        // but this file runs before the rest of the ResetDemoSite job runs.
        if (tenant()->ensembles->count() === 0) {
            tenant()->ensembles()->firstOrCreate(['name' => 'Hypothetical Harmony']);
        }

        $customFields = CustomField::factory()->count(10)->create();
        $faker = Faker::create();

        $voiceParts = VoicePart::pluck('id');

        $userRole = Role::query()->firstWhere(['name' => 'User']);

        // Add dummy users
        User::factory()
            ->count(90)
            ->create()
            ->each(function (User $user) use ($userRole, $voiceParts, $faker, $customFields): void {

                // Create matching singer
                $member = Membership::factory()
                    ->for($user)
                    ->hasAttached($customFields, fn() => ['value' => $faker->words(3, true)])
                    ->hasAttached($userRole)
                    ->state([
                        'onboarding_enabled' => $faker->boolean(30),
                    ])
                    ->create();

                // Create enrolment
                tenant()->ensembles()?->first()->enrolments()->create([
                    'membership_id' => $member->id,
                    'voice_part_id' => $voiceParts->random(),
                ]);

                // Generate realistic historical membership progression
                self::generateHistoricalMembershipProgression($member, $faker);
            });
    }

    /**
     * Generate historical membership progression for a dummy singer.
     *
     * Rules:
     * - Progression starts as prospects.
     * - Small percentage progress to archived-prospects and stay there.
     * - Remainder progress to active members; a portion of those eventually become inactive or archived-members.
     * - A portion of active members become inactive; some return to active membership and others become former
     *   members.
     * - Each singer starts at a random time and progresses after a random period.
     * - Prior to April 24, 2026 historical membership data was not tracked: insert one record prior to April 24,
     *   and continue progression after that date. NOTE: Dummy data is using April 24, 2024 temporarily.
     *
     * @param Membership $member
     * @param Generator $faker
     * @return void
     */
    public static function generateHistoricalMembershipProgression(Membership $member, Generator $faker): void
    {
        $distributions = [
            'prospectToArchived' => 20,
            'activeToInactive' => 30,
            'inactiveToActive' => 50,
            'inactiveToFormer' => 25,
            'activeToArchived' => 40,
        ];

        // Delete any status record auto-created by factory afterCreating hook
        $member->statuses()->delete();

        $historyCutoff = Carbon::create(2024, 4, 24)->startOfDay();
        $now = Carbon::now();

        // Random start date between 3 years ago and 2 weeks ago
        $minStart = $now->copy()->subYears(3)->startOfDay();
        $maxStart = $now->copy()->subWeeks(2)->startOfDay();
        $startSec = mt_rand($minStart->timestamp, $maxStart->timestamp);
        $startDate = Carbon::createFromTimestamp($startSec);

        $plannedTransitions = [];

        // Prospect -> Archived Prospect
        if ($faker->boolean($distributions['prospectToArchived'])) {
            $plannedTransitions[] = [
                'status' => SingerStatus::PROSPECTS,
                'date' => $startDate->copy(),
            ];

            $archivedDate = $startDate->copy()->addDays(mt_rand(14, 84));
            $plannedTransitions[] = [
                'status' => SingerStatus::ARCHIVED_PROSPECTS,
                'date' => $archivedDate,
            ];
        } else {
            // Prospect -> Active Member (-> maybe Archived Member)
            $plannedTransitions[] = [
                'status' => SingerStatus::PROSPECTS,
                'date' => $startDate->copy(),
            ];

            $memberDate = $startDate->copy()->addDays(mt_rand(14, 112));
            $plannedTransitions[] = [
                'status' => SingerStatus::MEMBERS,
                'date' => $memberDate,
            ];

            if ($faker->boolean($distributions['activeToInactive'])) {
                $inactiveDate = $memberDate->copy()->addDays(mt_rand(90, 180));
                $plannedTransitions[] = [
                    'status' => SingerStatus::INACTIVE_MEMBERS,
                    'date' => $inactiveDate,
                ];

                if ($faker->boolean($distributions['inactiveToActive'])) {
                    $plannedTransitions[] = [
                        'status' => SingerStatus::MEMBERS,
                        'date' => $inactiveDate->copy()->addDays(mt_rand(30, 120)),
                    ];
                } elseif ($faker->boolean($distributions['inactiveToFormer'])) {
                    $plannedTransitions[] = [
                        'status' => SingerStatus::ARCHIVED_MEMBERS,
                        'date' => $inactiveDate->copy()->addDays(mt_rand(30, 120)),
                    ];
                }
            } elseif ($faker->boolean($distributions['activeToArchived'])) {
                $archivedMemberDate = $memberDate->copy()->addDays(mt_rand(90, 730));
                $plannedTransitions[] = [
                    'status' => SingerStatus::ARCHIVED_MEMBERS,
                    'date' => $archivedMemberDate,
                ];
            }
        }

        // Membership history is tracked from the cutoff onwards. If a singer
        // became a member before then, move that transition and its successors
        // forward while preserving the progression intervals.
        $memberTransitionIndex = collect($plannedTransitions)
            ->search(fn (array $transition): bool => $transition['status'] === SingerStatus::MEMBERS);

        if ($memberTransitionIndex !== false && $plannedTransitions[$memberTransitionIndex]['date']->lt($historyCutoff)) {
            $shift = $historyCutoff->copy()->addSecond()->diffInSeconds($plannedTransitions[$memberTransitionIndex]['date']);

            foreach (array_keys($plannedTransitions) as $index) {
                if ($index >= $memberTransitionIndex) {
                    $plannedTransitions[$index]['date']->addSeconds($shift);
                }
            }
        }

        // Only keep transitions that have occurred on or before now()
        $validTransitions = array_values(array_filter($plannedTransitions, fn($t) => $t['date']->lte($now)));

        if (empty($validTransitions)) {
            $validTransitions[] = [
                'status' => SingerStatus::PROSPECTS,
                'date' => $startDate->lte($now) ? $startDate->copy() : $now->copy(),
            ];
        }

        // Separate transitions before April 24, 2026 and on/after April 24, 2026
        $preCutoffTransitions = array_values(array_filter($validTransitions, fn($t) => $t['date']->lt($historyCutoff)));
        $postCutoffTransitions = array_values(array_filter($validTransitions, fn($t) => $t['date']->gte($historyCutoff)));

        $finalStatuses = [];

        if (!empty($preCutoffTransitions)) {
            // Prior to April 24, 2026 historical data wasn't tracked; insert one record prior to April 24
            $preRecord = end($preCutoffTransitions);
            $finalStatuses[] = [
                'status' => $preRecord['status'],
                'date' => $preRecord['date'],
            ];

            // Continue the progression of the singer after that date
            foreach ($postCutoffTransitions as $postRecord) {
                $finalStatuses[] = $postRecord;
            }
        } else {
            // All transitions occurred on or after April 24, 2026
            $finalStatuses = $postCutoffTransitions;
        }

        $inactiveIndex = collect($finalStatuses)
            ->search(fn (array $transition): bool => $transition['status'] === SingerStatus::INACTIVE_MEMBERS);
        $memberIndex = collect($finalStatuses)
            ->search(fn (array $transition): bool => $transition['status'] === SingerStatus::MEMBERS);

        if ($inactiveIndex !== false && ($memberIndex === false || $memberIndex > $inactiveIndex)) {
            $inactiveDate = $finalStatuses[$inactiveIndex]['date'];
            $memberDate = $inactiveDate->lt($historyCutoff)
                ? $historyCutoff->copy()->addSecond()
                : $inactiveDate->copy()->subSecond();

            if ($inactiveDate->lt($historyCutoff)) {
                $finalStatuses[$inactiveIndex]['date'] = $historyCutoff->copy()->addSeconds(2);
                $inactiveDate = $finalStatuses[$inactiveIndex]['date'];
                $memberDate = $historyCutoff->copy()->addSecond();
            }

            array_splice($finalStatuses, $inactiveIndex, 0, [[
                'status' => SingerStatus::MEMBERS,
                'date' => $memberDate,
            ]]);
        }

        // Insert membership status records in chronological order
        $member->statuses()->createMany(collect($finalStatuses)->map(fn(array $statusData): array => [
            'status' => $statusData['status']->value,
            'created_at' => $statusData['date'],
            'updated_at' => $statusData['date'],
        ])->all());

        $firstStatusDate = $finalStatuses[0]['date'];
        $lastStatus = end($finalStatuses);

        $memberTransition = collect($validTransitions)->firstWhere('status', SingerStatus::MEMBERS);
        $joinedAt = $memberTransition ? $memberTransition['date'] : $firstStatusDate;

        $member->created_at = $firstStatusDate;
        $member->updated_at = $lastStatus['date'];
        $member->joined_at = $joinedAt;

        if ($lastStatus['status'] === SingerStatus::MEMBERS) {
            $member->paid_until = $now->copy()->addMonths(mt_rand(-1, 11));
        } elseif ($memberTransition) {
            $member->paid_until = $lastStatus['date']->copy()->addMonths(mt_rand(1, 12));
        } else {
            $member->paid_until = null;
        }

        $member->saveQuietly();

        if ($member->user) {
            $member->user->created_at = $firstStatusDate;
            $member->user->updated_at = $lastStatus['date'];
            $member->user->last_login = $lastStatus['status'] === SingerStatus::MEMBERS
                ? $now->copy()->subDays(mt_rand(0, 30))
                : $lastStatus['date']->copy()->subDays(mt_rand(0, 30));
            $member->user->saveQuietly();
        }
    }
}
