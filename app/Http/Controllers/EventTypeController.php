<?php

namespace App\Http\Controllers;

use App\Models\EventType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventTypeController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(EventType::class, 'event_type');
    }

    public function index(): Response
    {
        return Inertia::render('EventTypes/Index', ['categories' => EventType::withCount('events')->get()->values()]);
    }

    public function store(Request $request): RedirectResponse
    {
        EventType::create($request->validate([
            'title' => 'required|max:255',
        ]));

        return redirect()
            ->back()
            ->with(['status' => 'Event Type created.']);
    }

    public function update(Request $request, EventType $event_type): RedirectResponse
    {
        $event_type->update($request->validate([
            'title' => 'required|max:255',
        ]));

        return redirect()
            ->back()
            ->with(['status' => 'Event Type saved.']);
    }

    public function destroy(EventType $event_type): RedirectResponse
    {
        $event_type->delete();

        return redirect()
            ->back()
            ->with(['status' => 'Event Type deleted.']);
    }
}
