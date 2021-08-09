<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Singer;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
	public function index(Event $event): View
	{
        $this->authorize('viewAny', Attendance::class);

        $voice_parts = Singer::with([
                'user',
                'voice_part',
                'attendances' => function ($query) use ($event) {
                    $query->where('event_id', '=', $event->id);
                },
            ])
            ->get()
            ->sortBy('user.first_name')
            ->each(fn($singer) => $singer->attendance = $singer->attendances->first())
            ->groupBy('voice_part.id')
            ->map(function($singers) {
                $part = $singers->first()->voice_part ?? VoicePart::getNullVoicePart();
                $part->singers = $singers;
                return $part;
            });
        $voice_parts = $this->moveNoPartToEnd($voice_parts);

        return view('events.attendances.index', [
			'event' => $event,
			'voice_parts' => $voice_parts,
		]);
	}

	public function updateAll(Event $event, Request $request): RedirectResponse
	{
		$this->authorize('create', Attendance::class);

		$absent_reason = $request->input('absent_reason');
		$responses = $request->input('attendance_response');
		foreach ($responses as $singer_id => $response) {
			$event->attendances()->updateOrCreate(
				['singer_id' => $singer_id],
				[
					'response' => $response,
					'absent_reason' => $absent_reason[$singer_id],
				],
			);
		}

		return redirect()
			->route('events.show', ['event' => $event])
			->with(['status' => 'Attendance recorded.']);
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
