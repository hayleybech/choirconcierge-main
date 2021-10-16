<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Singer;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;

class AttendanceReportController extends Controller
{
	public function __invoke(): View
	{
		$this->authorize('viewAny', Attendance::class);

		$all_events = Event::with([])
			->orderBy('start_date')
			->filter()
			->get();

        $voice_parts = Singer::active()
            ->with(['user', 'voice_part', 'attendances'])->get()
            ->sortBy('user.first_name')
            ->groupBy('voice_part.id')
            ->map(function($singers) {
                $part = $singers->first()->voice_part ?? VoicePart::getNullVoicePart();
                $part->singers = $singers;
                return $part;
            });
        $voice_parts = $this->moveNoPartToEnd($voice_parts);

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
