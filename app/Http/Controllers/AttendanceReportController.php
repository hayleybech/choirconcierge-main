<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Singer;
use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceReportController extends Controller
{
    public function __invoke(): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $all_events = Event::with([])
            ->orderBy('start_date')
            ->get();

        $singers = $this->getSingers($all_events);

        $all_events->each(function ($event) use ($singers) {
            $event->singersPresent = $event->singers_attendance('present')->active()->get()->count();
            $event->percentPresent = floor($event->singersPresent / $singers->count() * 100);
        });

        $avg_singers_per_event = round(
            $all_events->reduce(static function ($carry, $event) {
                return $carry + $event->singers_attendance('present')->count();
            }, 0) / $all_events->count(),
            2,
        );

        return Inertia::render('Events/AttendanceReport', [
            'voiceParts' => $this->getVoiceParts($singers)->values(),
            'events' => $all_events->where('start_date', '<', now())->values(),
            'numSingers' => $singers->count(),
            'avgSingersPerEvent' => $avg_singers_per_event,
            'avgEventsPerSinger' => $this->getAverageEventsPerSinger(),
        ]);
    }

    private function getAverageEventsPerSinger(): float
    {
        return round(
            Singer::active()
                ->with(['attendances'])
                ->get()
                ->reduce(fn ($carry, $singer) =>
                    $carry + $singer
                        ->attendances()
                        ->where('response', 'present')
                        ->count()
                , 0) / Singer::all()->count(),
            2,
        );
    }

    private function getVoiceParts($singers): \Illuminate\Support\Collection|Collection
    {
        return VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function ($part) use ($singers) {
                $part->singers = $singers
                    ->filter(fn($singer) => $singer->voice_part_id === $part->id)
                    ->values();

                return $part;
            });
    }

    private function getSingers(Collection $events)
    {
        return Singer::with(['user', 'attendances'])
            ->get()
            ->append('user_avatar_thumb_url')
            ->each(function ($singer) use ($events) {
                $singer->timesPresent = $singer->attendances->where('response', 'present')->count();
                $singer->percentPresent = floor($singer->timesPresent / $events->count() * 100);
            });
    }
}
