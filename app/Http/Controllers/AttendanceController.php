<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Singer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Event $event)
    {
        $this->authorize('viewAny', Attendance::class);

        $singers = Singer::all();
        foreach($singers as $singer)
        {
            $singer->attendance = $event->attendances()->where('singer_id', '=', $singer->id)->first();
        }

        return view('events.attendances.index', [
            'event'   => $event,
            'singers' => $singers,
        ]);
    }

    public function updateAll(Event $event, Request $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $responses = $request->input('attendance_response');
        foreach($responses as $singer_id => $response){
            $event->attendances()->updateOrCreate(
                ['singer_id' => $singer_id],
                ['response'  => $response]
            );
        }

        return redirect()->route('events.show', ['event' => $event])->with(['status' => 'Attendance recorded.']);
    }
}
