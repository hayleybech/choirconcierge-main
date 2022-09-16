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

        $request->validate([
            'description' => 'nullable',
            'duration' => 'numeric|min:0',
            'song_id' => 'nullable|sometimes|exists:songs,id'
        ]);

        $event->activities()->create($request->only('description', 'duration', 'song_id'));

        return redirect()->back()->with(['status' => 'Item added.']);
    }

    public function update(Request $request, Event $event, EventActivity $activity): RedirectResponse
    {
        $this->authorize('update', $event);

        $request->validate([
            'description' => 'nullable',
            'duration' => 'numeric|min:0',
            'song_id' => 'nullable|sometimes|exists:songs,id'
        ]);

        $activity->update($request->only('description', 'duration', 'song_id'));

        return redirect()->back()->with(['status' => 'Item saved.']);
    }

    public function destroy(Event $event, EventActivity $activity): RedirectResponse
    {
        $this->authorize('update', $event);

        $activity->delete();

        return redirect()->back()->with(['status' => 'Item deleted']);
    }
}
