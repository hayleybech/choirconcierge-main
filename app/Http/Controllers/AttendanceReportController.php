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

        $events->each(function ($event) use ($singers) {
            $event->singersPresent = $event->singers_attendance('present')->active()->get()->count();
            $event->percentPresent = $singers->count() > 0 ? floor($event->singersPresent / $singers->count() * 100) : null;
        });

        $avg_singers_per_event = round(
            $events->count() > 0
                ? $events->reduce(static function ($carry, $event) {
                    return $carry + $event->singers_attendance('present')->count();
                }, 0) / $events->count()
                : null,
            2,
        );

        return Inertia::render('Events/AttendanceReport', [
            'voiceParts' => $this->getVoiceParts($singers)->values(),
            'events' => $events->values(),
            'eventTypes' => EventType::all()->values(),
            'defaultEventType' => $defaultEventType,
            'defaultStartsAfter' => $defaultStartsAfter,
            'defaultStartsBefore' => $defaultStartsBefore,
            'numSingers' => $singers->count(),
            'avgSingersPerEvent' => $avg_singers_per_event,
            'avgEventsPerSinger' => $this->getAverageEventsPerSinger(),
        ]);
    }

    private function getAverageEventsPerSinger(): float
    {
        return round(
            Membership::active()
                ->with(['attendances'])
                ->get()
                ->reduce(fn ($carry, $singer) =>
                    $carry + $singer
                        ->attendances()
                        ->where('response', 'present')
                        ->count()
                , 0) / Membership::all()->count(),
            2,
        );
    }

    private function getVoiceParts($singers): \Illuminate\Support\Collection|Collection
    {
        return VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function ($part) use ($singers) {
                $part->members = $singers
                    ->filter(fn($singer) => $singer->enrolments
                        ->contains(fn ($enrolment) => $enrolment->voice_part_id === $part->id))
                    ->values();

                return $part;
            });
    }

    private function getSingers(Collection $events)
    {
        return Membership::with(['user', 'attendances', 'enrolments'])
            ->active()
            ->get()
            ->append('user_avatar_thumb_url')
            ->each(function ($singer) use ($events) {
                $singer->timesPresent = $singer
	                ->attendances
	                ->whereIn('event_id', $events->pluck('id'))
	                ->where('response', 'present')
	                ->count();
                $singer->percentPresent = $events->count() > 0 ? floor($singer->timesPresent / $events->count() * 100) : null;
            });
    }
}
