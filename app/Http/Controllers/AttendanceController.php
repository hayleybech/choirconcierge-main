<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\VoicePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceController extends Controller
{
    public function index(Event $event): Response
    {
        $this->authorize('viewAny', Attendance::class);

        $event->createMissingAttendanceRecords();

        $event->load(['attendances' => fn ($query) => $query->with('member.user')->whereHas('member', fn ($query) => $query->active())]);

        $event->attendances->each(fn ($attendance) => $attendance->member->user->append('avatar_url'));

        $voice_parts = VoicePart::all()
            ->push(VoicePart::getNullVoicePart())
            ->map(function ($part) use ($event) {
                $part->members = $event->attendances
                    ->filter(fn ($attendance) => $attendance->member->voice_part_id === $part->id)
                    ->values();

                return $part;
            });

        return Inertia::render('Events/Attendance/Index', [
            'event' => $event,
            'voice_parts' => $voice_parts->values(),
        ]);
    }

    public function update(Event $event, Membership $singer, Request $request): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $request->validate([
            'response' => ['in:unknown,absent,absent_apology,late,present'],
        ]);

        $event->attendances()
            ->updateOrCreate(
                ['membership_id' => $singer->id],
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
        foreach ($responses as $membership_id => $response) {
            $event->attendances()->updateOrCreate(
                ['membership_id' => $membership_id],
                [
                    'response' => $response,
                    'absent_reason' => $absent_reason[$membership_id],
                ],
            );
        }

        return redirect()
            ->route('events.show', ['event' => $event])
            ->with(['status' => 'Attendance recorded.']);
    }
}
