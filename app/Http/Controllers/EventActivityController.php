<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventActivityRequest;
use App\Models\Event;
use App\Models\EventActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EventActivityController extends Controller
{
    public function index(Event $event): Response
    {
        return Inertia::render('Events/Activities/Index', [
            'event' => $event,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Event $event, EventActivityRequest $request): RedirectResponse
    {
        EventActivity::create($request->safe()->merge([
            'event_id' => $event->id,
        ])->toArray());

        return redirect()->route('events.activities.index', $event)
            ->with(['status' => 'Activity added.']);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EventActivity  $eventActivity
     * @return \Illuminate\Http\Response
     */
    public function show(EventActivity $eventActivity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EventActivity  $eventActivity
     * @return \Illuminate\Http\Response
     */
    public function edit(EventActivity $eventActivity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EventActivity  $eventActivity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EventActivity $eventActivity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EventActivity  $eventActivity
     * @return \Illuminate\Http\Response
     */
    public function destroy(EventActivity $eventActivity)
    {
        //
    }
}
