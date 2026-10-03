<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class AttendanceReportController extends Controller
{
    private const PRESENT_RESPONSES = ['present', 'late'];

    public function __invoke(): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $defaultEventType = EventType::where('title', 'Performance')->value('id');
        $defaultStartsAfter = now()->subYear();
        $defaultStartsBefore = now();

        $events = QueryBuilder::for(Event::class)
            ->with([])
            ->allowedFilters([
                AllowedFilter::exact('type.id')
                    ->default([$defaultEventType]),
                AllowedFilter::scope('starts_after')
                    ->default($defaultStartsAfter),
                AllowedFilter::scope('starts_before')
                    ->default($defaultStartsBefore),
            ])
            ->orderBy('start_date')
            ->get();

        $singers = $this->getSingers($events);

        return Inertia::render('Events/AttendanceReport', [
            'voiceParts' => $this->getVoiceParts($singers)->values(),
            'events' => $events->values(),
            'eventTypes' => EventType::all()->values(),
            'defaultEventType' => $defaultEventType,
            'defaultStartsAfter' => $defaultStartsAfter,
            'defaultStartsBefore' => $defaultStartsBefore,
            'numSingers' => $singers->count(),
            'avgSingersPerEvent' => $events->count() > 0
                ? round($events->sum('singersPresent') / $events->count(), 2)
                : null,
            'avgEventsPerSinger' => $singers->count() > 0
                ? round($singers->sum('timesPresent') / $singers->count(), 2)
                : null,
        ]);
    }

    private function getVoiceParts($singers): \Illuminate\Support\Collection|Collection
    {
        return VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function ($part) use ($singers) {
                $part->members = $singers
                    ->filter(fn($singer) => $singer->enrolments
                        ->contains(fn($enrolment) => $enrolment->voice_part_id === $part->id))
                    ->values();

                return $part;
            });
    }

    /**
     * Determine which singers are considered for each event, then calculate totals for events and singers.
     *
     * Before membership status history was tracked, a singer is considered for an event if their attendance
     * was recorded for it. Afterwards, a singer is considered if they were an active member at the time.
     */
    private function getSingers(Collection $events): Collection
    {
        $memberships = Membership::with([
            'user',
            'enrolments',
            'statuses',
            'attendances' => fn($query) => $query->whereIn('event_id', $events->pluck('id')),
        ])
            ->get();

        $events->each(function (Event $event) use ($memberships) {
            $event->isBeforeHistory = $event->isBeforeMembershipHistory();

            $considered = $memberships->filter(fn(Membership $singer) => $singer->isConsideredForEvent($event));

            $event->consideredSingerIds = $considered->pluck('id')->values();
            $event->numSingers = $considered->count();
            $event->singersPresent = $considered
                ->filter(fn(Membership $singer) => $singer->attendances
                    ->where('event_id', $event->id)
                    ->whereIn('response', self::PRESENT_RESPONSES)
                    ->isNotEmpty())
                ->count();
            $event->percentPresent = $event->numSingers > 0
                ? floor($event->singersPresent / $event->numSingers * 100)
                : null;
        });

        return $memberships
            ->filter(fn(Membership $singer) => $events->contains(
                fn(Event $event) => $event->consideredSingerIds->contains($singer->id)
            ))
            ->values()
            ->makeHidden('statuses')
            ->append('user_avatar_thumb_url')
            ->each(function (Membership $singer) use ($events) {
                $consideredEventIds = $events
                    ->filter(fn(Event $event) => $event->consideredSingerIds->contains($singer->id))
                    ->pluck('id');

                $singer->numEvents = $consideredEventIds->count();
                $singer->timesPresent = $singer
                    ->attendances
                    ->whereIn('event_id', $consideredEventIds)
                    ->whereIn('response', self::PRESENT_RESPONSES)
                    ->count();
                $singer->percentPresent = $singer->numEvents > 0
                    ? floor($singer->timesPresent / $singer->numEvents * 100)
                    : null;
            });
    }
}
