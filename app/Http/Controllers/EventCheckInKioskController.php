<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventCheckInKioskController extends Controller
{
    public function index(Event $event): Response
    {
        $this->authorize('create', Attendance::class);

        return Inertia::render('Events/Attendance/CheckInKiosk', [
            'event' => $event,
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('create', Attendance::class);

        $validated = $request->validate([
            'user' => [
                'required',
                'exists:App\Models\User,id',
            ],
        ]);

        $event->attendances()
            ->updateOrCreate(
                ['membership_id' => Membership::firstWhere('user_id', $validated['user'])->id],
                ['response' => EventCheckInController::getAttendanceByEvent($event)]
            );

        return redirect()
            ->back()
            ->with(['status' => 'Attendance recorded.']);
    }
}
