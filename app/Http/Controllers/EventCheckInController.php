<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use App\Models\Membership;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventCheckInController extends Controller
{
    public function index(Event $event): Response
    {
        // @todo auth
        return Inertia::render('Events/Attendance/CheckIn', [
            'event' => $event,
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        // @todo check auth
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
                ['response' => 'present']
            );

        return redirect()
            ->route('events.check-in', ['event' => $event])
            ->with(['status' => 'Attendance recorded.']);
    }
}
