<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class RsvpFromNotificationController extends Controller
{
    public function __invoke(Request $request, Event $event, User $user)
    {
        $request->validate([
            'response' => ['required', 'in:yes,no'],
        ]);

        $event->rsvps()->updateOrCreate(
            ['membership_id' => $user->membership->id],
            ['response' => $request->input('response')]
        );

        return redirect()
            ->route(auth()->user() ? 'dash' : 'login')
            ->with(['status' => 'RSVP saved.']);
    }
}
