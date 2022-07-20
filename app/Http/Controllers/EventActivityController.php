<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EventActivityController extends Controller
{
    public function store(Request $request, Event $event): RedirectResponse
    {
        $this->authorize('update', $event);

        $event->activities()->create($request->validate([
            'description' => 'nullable',
            'duration' => 'numeric|min:0',
        ]));

        return redirect()->back()->with(['status' => 'Item added.']);
    }

    public function destroy(Event $event, EventActivity $activity): RedirectResponse
    {
        $this->authorize('update', $event);

        $activity->delete();

        return redirect()->back()->with(['status' => 'Item deleted']);
    }
}
