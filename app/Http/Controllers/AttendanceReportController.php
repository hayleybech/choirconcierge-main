<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Singer;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceReportController extends Controller
{
	public function __invoke(): View|Response
	{
		$this->authorize('viewAny', Attendance::class);

		$all_events = Event::with([])
			->orderBy('start_date')
			->filter()
			->get();

//        $voice_parts = Singer::active()
////            ->with(['user', 'voice_part', 'attendances'])
//            ->get()
////            ->sortBy('user.first_name')
//            ->groupBy('voice_part.id')
//            ->map(function($singers) {
//                $part = $singers->first()->voice_part ?? VoicePart::getNullVoicePart();
//                $part->singers = $singers;
//                return $part;
//            });
//        $voice_parts = $this->moveNoPartToEnd($voice_parts);

        $singers = Singer::with(['user', 'attendances'])
            ->get()
            ->each
            ->append('user_avatar_thumb_url')
            ->each(function ($singer) use ($all_events) {
                $singer->timesPresent = $singer->attendances->where('response', 'present')->count();
                $singer->percentPresent = floor($singer->timesPresent / $all_events->count() * 100 );
            });

        $all_events->each(function ($event) use ($singers) {
            $event->singersPresent = $event->singers_attendance('present')->active()->get()->count();
            $event->percentPresent = floor($event->singersPresent / $singers->count() * 100);
        });

        $voice_parts = VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function($part) use ($singers) {
                $part->singers = $singers
                    ->filter(fn($singer) => $singer->voice_part_id === $part->id)
                    ->values();
                return $part;
            });

		$avg_singers_per_event = round(
			$all_events->reduce(static function ($carry, $event) {
				return $carry + $event->singers_attendance('present')->count();
			}, 0) / $all_events->count(),
			2,
		);

		$avg_events_per_singer = round(
			Singer::active()
                ->with(['attendances'])
                ->get()
                ->reduce(static function ($carry, $singer) {
                    return $carry +
                        $singer
                            ->attendances()
                            ->where('response', 'present')
                            ->count();
                }, 0) / Singer::all()->count(),
            2,
        );

        if(config('features.rebuild')) {
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Events/AttendanceReport', [
                'voiceParts' => $voice_parts->values(),
                'events' => $all_events->where('start_date', '<', now())->values(),
                'numSingers' => $singers->count(),
                'avgSingersPerEvent' => $avg_singers_per_event,
                'avgEventsPerSinger' => $avg_events_per_singer,
            ]);
        }

		return view('events.reports.attendance', [
			'voice_parts' => $voice_parts,
			'events' => $all_events->where('start_date', '<', now()),
			'filters' => Event::getFilters(),
			'avg_singers_per_event' => $avg_singers_per_event,
			'avg_events_per_singer' => $avg_events_per_singer,
		]);
	}

    private function moveNoPartToEnd($collection){
        return $collection->reject(function($value){
            return $value->title === 'No Part';
        })
            ->merge($collection->filter(function($value){
                return $value->title === 'No Part';
            })
            );
    }
}
