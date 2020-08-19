<?php


namespace App\Http\Controllers;


use App\Models\Event;
use App\Models\Rsvp;
use Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'rsvp_response' => 'required',
        ]);

        $event->rsvps()->create([
            'response'  => $request->input('rsvp_response'),
            'singer_id' => Auth::id(),
        ]);

        return redirect()->route('events.show', [$event])->with(['status' => 'RSVP saved.']);
    }

    public function destroy(Event $event, Rsvp $rsvp): RedirectResponse
    {
        $rsvp->delete();

        return redirect()->route('events.show', [$event])->with(['status' => 'RSVP deleted.']);
    }
}