<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Singer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
	public function index(Event $event): View
	{
		$this->authorize('viewAny', Attendance::class);

		$singers = Singer::all();
		foreach ($singers as $singer) {
			$singer->attendance = $event
				->attendances()
				->where('singer_id', '=', $singer->id)
				->first();
		}

		return view('events.attendances.index', [
			'event' => $event,
			'singers' => $singers,
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
}
