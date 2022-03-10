<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Singer;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function index(Event $event): View|Response
    {
        $this->authorize('viewAny', Attendance::class);

        $event->createMissingAttendanceRecords();

        $event->load(['attendances' => function ($query) {
            return $query->with('singer.user')
                ->whereHas('singer', fn ($query) => $query->active());
        }]);

        $event->attendances->each(fn ($attendance) => $attendance->singer->user->append('avatar_url'));

        $voice_parts = VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function ($part) use ($event) {
                $part->singers = $event->attendances
                    ->filter(fn ($attendance) => $attendance->singer->voice_part_id === $part->id)
                    ->values();

                return $part;
            });

        return Inertia::render('Events/Attendance/Index', [
            'event' => $event,
            'voice_parts' => $voice_parts->values(),
        ]);
    }

    public function update(Event $event, Singer $singer, Request $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $request->validate([
            'response' => ['in:unknown,absent,absent_apology,late,present'],
        ]);

        $event->attendances()
            ->updateOrCreate(
                ['singer_id' => $singer->id],
                [
                    'response' => $request->input('response'),
                    'absent_reason' => $request->input('absent_reason'),
                ]
            );

        return redirect()
            ->route('events.attendances.index', ['event' => $event])
            ->with(['status' => 'Attendance recorded.']);
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
