<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Singer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Event $event)
    {
        return view('events.attendances.index', [
            'event'   => $event,
            'singers' => Singer::all(),
        ]);
    }

    public function updateAll(Event $event, Request $request): RedirectResponse
    {
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
