<?php

namespace Database\Seeders\Dummy;

use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DummyEventSeeder extends Seeder
{
    public function run(): void
    {
        $types = EventType::all();

        self::insertHistoricalAndUpcomingEvents($types);
        self::insertDemoEvent($types);
    }

    private static function insertHistoricalAndUpcomingEvents(Collection $types): void
    {
        $rehearsalType = $types->firstWhere('title', 'Rehearsal');
        $performanceType = $types->firstWhere('title', 'Performance');
        $workshopType = $types->firstWhere('title', 'Workshop');
        $socialType = $types->firstWhere('title', 'Social Event');
        $meetingType = $types->firstWhere('title', 'Meeting');

        $ensemble = tenant()->ensembles()->first();

        $now = Carbon::now();

        // 1. Weekly Rehearsals from 3 years ago to 3 months into future (every Tuesday 7:00pm - 9:30pm, call time 6:45pm)
        $currentDate = $now->copy()->subYears(3)->next(Carbon::TUESDAY);
        $endDate = $now->copy()->addMonths(3);

        while ($currentDate->lte($endDate)) {
            $startTime = $currentDate->copy()->setTime(19, 0, 0);

            $ensemble?->events()->syncWithoutDetaching([Event::create([
                'title' => 'Weekly Rehearsal',
                'call_time' => $currentDate->copy()->setTime(18, 45, 0),
                'start_date' => $startTime,
                'end_date' => $currentDate->copy()->setTime(21, 30, 0),
                'location_name' => "St Peter's Community Hall",
                'location_address' => '15 St Peter Street, Sydney NSW 2000',
                'description' => 'Regular weekly choir rehearsal.',
                'type_id' => $rehearsalType?->id,
                'created_at' => $startTime->copy()->subDays(14),
                'updated_at' => $startTime->copy()->subDays(14),
            ])->id]);

            $currentDate->addWeek();
        }

        // 2. Periodic Performances (every ~6-8 weeks over past 2 years and upcoming)
        $performanceTitles = [
            'Autumn Gala Concert',
            'Community Voices Festival',
            'Winter Showcase',
            'Choral Championship',
            'Spring Serenade',
            'Annual Choral Spectacular',
            'End of Year Showcase',
            'Summer Harmony Concert',
            'City Recital Series',
            'Charity Benefit Concert',
            'Mid-Year Choral Celebration',
            'Harbour Symphony Performance',
        ];

        $perfDate = $now->copy()->subYears(2)->addWeeks(6)->next(Carbon::SATURDAY);
        $titleIndex = 0;

        while ($perfDate->lte($endDate)) {
            $title = $performanceTitles[$titleIndex % count($performanceTitles)];
            $titleIndex++;

            $startTime = $perfDate->copy()->setTime(19, 0, 0);
            $ensemble?->events()->syncWithoutDetaching([Event::create([
                'title' => $title,
                'call_time' => $perfDate->copy()->setTime(17, 30, 0),
                'start_date' => $startTime,
                'end_date' => $perfDate->copy()->setTime(21, 30, 0),
                'location_name' => 'Sydney Town Hall',
                'location_address' => '483 George St, Sydney NSW 2000',
                'description' => 'Major public performance showcasing our repertoire.',
                'type_id' => $performanceType?->id,
                'created_at' => $startTime->copy()->subMonths(2),
                'updated_at' => $startTime->copy()->subMonths(2),
            ])->id]);

            $perfDate->addWeeks(mt_rand(6, 8))->next(Carbon::SATURDAY);
        }

        // 3. Workshops & Social Events (every ~3-4 months)
        $specialEvents = [
            ['title' => 'Vocal Technique Workshop', 'type' => $workshopType, 'location' => 'Sydney Conservatorium of Music', 'address' => '1 Conservatorium Rd, Sydney NSW 2000'],
            ['title' => 'Choir Social Dinner', 'type' => $socialType, 'location' => 'The Grand Hotel', 'address' => '30 Hunter St, Sydney NSW 2000'],
            ['title' => 'Annual General Meeting', 'type' => $meetingType, 'location' => "St Peter's Community Hall", 'address' => '15 St Peter Street, Sydney NSW 2000'],
            ['title' => 'Guest Clinician Masterclass', 'type' => $workshopType, 'location' => 'Sydney Conservatorium of Music', 'address' => '1 Conservatorium Rd, Sydney NSW 2000'],
        ];

        $specialDate = $now->copy()->subYears(2)->addMonths(2)->next(Carbon::SUNDAY);
        $specialIndex = 0;

        while ($specialDate->lte($endDate)) {
            $special = $specialEvents[$specialIndex % count($specialEvents)];
            $specialIndex++;

            $startTime = $specialDate->copy()->setTime(14, 0, 0);
            $ensemble?->events()->syncWithoutDetaching([Event::create([
                'title' => $special['title'],
                'call_time' => $specialDate->copy()->setTime(13, 30, 0),
                'start_date' => $startTime,
                'end_date' => $specialDate->copy()->setTime(17, 0, 0),
                'location_name' => $special['location'],
                'location_address' => $special['address'],
                'description' => $special['title'] . ' for all active members.',
                'type_id' => $special['type']?->id,
                'created_at' => $startTime->copy()->subMonths(1),
                'updated_at' => $startTime->copy()->subMonths(1),
            ])->id]);

            $specialDate->addMonths(mt_rand(3, 4))->next(Carbon::SUNDAY);
        }
    }

    private static function insertDemoEvent(Collection $types): void
    {
        tenant()->ensembles()->first()?->events()->syncWithoutDetaching([Event::create([
            'title' => 'Rehearsal',
            'start_date' => now()->addHour(),
            'end_date' => now()->addHours(3),
            'call_time' => now()->addHour()->subMinutes(15),
            'location_place_id' => 'ChIJ3S-JXmauEmsRUcIaWtf4MzE',
            'location_name' => 'Sydney Opera House',
            'location_address' => 'Sydney Opera House, Sydney NSW, Australia',
            'description' => 'This demo event will repeat every day. Check out our favourite Events-related feature: A widget on the dashboard with a mini-map to any event on today!',
            'type_id' => $types->firstWhere('title', 'Rehearsal')?->id,

            'is_repeating' => true,
            'repeat_until' => now()->addMonth(),
            'repeat_frequency_amount' => 1,
            'repeat_frequency_unit' => 'day',
        ])->id]);
    }
}
