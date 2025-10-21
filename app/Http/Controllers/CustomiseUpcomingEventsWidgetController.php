<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CustomiseUpcomingEventsWidgetController extends Controller
{
    public function __invoke(Request $request) {
        Gate::authorize('create', Event::class);

        $request->validate([
            'event_categories' => ['required', 'exists:event_types,id'],
        ]);

        tenant()->update(['widgets_upcoming_events_categories' => $request->input('event_categories')]);
    }
}
