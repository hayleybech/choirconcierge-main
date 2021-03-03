<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditRecurringEventController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param Request $request
     * @param Event $event
     * @param String $mode 'single'|'following'|'all'
     * @return RedirectResponse
     */
    public function __invoke(Request $request, Event $event, String $mode): RedirectResponse
    {
        $edit_urls = [
            'single' => route('events.edit', ['event' => $event, 'mode' => $mode]),
            'following' => route('events.edit', ['event' => $event, 'mode' => $mode]),
            'all' => route('events.edit', ['event' => $event->repeat_parent, 'mode' => $mode]),
        ];
        return redirect($edit_urls[$mode]);
    }
}
