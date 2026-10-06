<?php

use App\Enums\SingerStatus;
use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Tenant;
use Carbon\Carbon;
use Database\Seeders\CriticalDataSeeder;
use Database\Seeders\DummyDataSeeder;

beforeEach(function () {
    Tenant::find('test')?->delete();
    $tenant = Tenant::factory()
        ->withDomain()
        ->create(['id' => 'test', 'name' => 'Test Choir', 'timezone' => 'Australia/Perth']);

    tenancy()->initialize($tenant);
});

it('seeds dummy singers with progression and correct pre-April 24 tracking', function () {
    $members = Membership::with('statuses', 'user')->get();

    expect($members->count())->toBeGreaterThanOrEqual(30);

    $historyCutoff = Carbon::create(2024, 4, 24)->startOfDay();

    $allowedStatuses = [
        SingerStatus::PROSPECTS,
        SingerStatus::MEMBERS,
        SingerStatus::INACTIVE_MEMBERS,
        SingerStatus::ARCHIVED_PROSPECTS,
        SingerStatus::ARCHIVED_MEMBERS,
    ];

    foreach ($members as $member) {
        $statuses = $member->statuses->sortBy('created_at')->values();

        expect($statuses)->not->toBeEmpty();

        // Check valid status values
        foreach ($statuses as $statusRecord) {
            expect($allowedStatuses)->toContain($statusRecord->status);
        }

        // Check chronological order
        for ($i = 0; $i < $statuses->count() - 1; $i++) {
            expect($statuses[$i]->created_at->lte($statuses[$i + 1]->created_at))->toBeTrue();
        }

        // Check pre-April 24, 2026 tracking rule:
        // Prior to April 24 we didn't track historical membership data; at most one record prior to April 24.
        $preCutoff = $statuses->filter(fn ($s) => $s->created_at->lt($historyCutoff));
        expect($preCutoff->count())->toBeLessThanOrEqual(1);

        // Check progression sequence logic:
        $statusValues = $statuses->pluck('status')->all();
        if (in_array(SingerStatus::ARCHIVED_PROSPECTS, $statusValues, true)) {
            // Archived prospects must not progress to members or archived-members
            expect($statusValues)->not->toContain(SingerStatus::MEMBERS);
            expect($statusValues)->not->toContain(SingerStatus::ARCHIVED_MEMBERS);
        }

        if (in_array(SingerStatus::ARCHIVED_MEMBERS, $statusValues, true)) {
            // Archived members must have been members (or prospect before cutoff if pre-cutoff single record was member)
            expect($member->joined_at)->not->toBeNull();
        }
    }

    $histories = $members->map(fn (Membership $member) => $member->statuses->sortBy('created_at')->pluck('status')->all());

    expect($histories->flatten())->toContain(SingerStatus::INACTIVE_MEMBERS);

    foreach ($histories as $statuses) {
        if (! in_array(SingerStatus::INACTIVE_MEMBERS, $statuses, true)) {
            continue;
        }

        expect($statuses)->toContain(SingerStatus::MEMBERS);
        expect(array_search(SingerStatus::INACTIVE_MEMBERS, $statuses, true))
            ->toBeGreaterThan(array_search(SingerStatus::MEMBERS, $statuses, true));
    }
});

it('only generates attendance records for singers who were active members at event start date', function () {
    $attendances = Attendance::with('event', 'member.statuses')->get();

    expect($attendances)->not->toBeEmpty();

    foreach ($attendances as $attendance) {
        $event = $attendance->event;
        $member = $attendance->member;
        $eventDate = $event->start_date;

        // The event must be in the past
        expect($eventDate->lte(now()))->toBeTrue();

        // The member must have been an active member at eventDate
        $statusAtEvent = $member->statuses
            ->filter(fn ($s) => $s->created_at->lte($eventDate))
            ->sortBy('created_at')
            ->last();

        expect($statusAtEvent)->not->toBeNull();
        expect($statusAtEvent->status)->toBe(SingerStatus::MEMBERS);
    }
});

it('includes late deemed absent attendance records', function () {
    expect(Attendance::query()->where('response', 'late_deemed_absent')->exists())->toBeTrue();
});

it('includes attendance outliers such as nearly-always and rarely attending singers', function () {
    $members = Membership::with('attendances', 'statuses')->get();

    // Group active members with at least 10 attendance opportunities
    $attendanceRates = [];

    foreach ($members as $member) {
        $totalRecords = $member->attendances->count();
        if ($totalRecords >= 10) {
            $attended = $member->attendances->whereIn('response', ['present', 'late'])->count();
            $attendanceRates[] = $attended / $totalRecords;
        }
    }

    expect($attendanceRates)->not->toBeEmpty();

    $maxRate = max($attendanceRates);
    $minRate = min($attendanceRates);

    // There should be a high attendee outlier (>90%) and a rare attendee outlier (<35%)
    expect($maxRate)->toBeGreaterThanOrEqual(0.90);
    expect($minRate)->toBeLessThanOrEqual(0.35);
});
