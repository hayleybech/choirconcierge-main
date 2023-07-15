<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\PlacementRequest;
use App\Models\Placement;
use App\Models\Membership;
use App\Models\Task;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SingerPlacementController extends Controller
{
    const PLACEMENT_TASK_ID = 2;

    public function create(Membership $singer): View|Response
    {
        $this->authorize('create', [Placement::class, $singer]);

        $singer->load('user');

        return Inertia::render('Singers/Placements/Create', [
            'singer' => $singer,
            'voice_parts' => VoicePart::all()->prepend(VoicePart::getNullVoicePart())->values(),
        ]);
    }

    public function store(Membership $singer, PlacementRequest $request): RedirectResponse
    {
        $this->authorize('create', [Placement::class, $singer]);

        $singer->placement()->create($request->safe()->except('voice_part_id'));

        if ($singer->onboarding_enabled) {
            // Mark matching task completed
            //$task = $singer->tasks()->where('name', 'Voice Placement')->get();
            $singer->tasks()->updateExistingPivot(self::PLACEMENT_TASK_ID, ['completed' => true]);

            event(new TaskCompleted(Task::find(self::PLACEMENT_TASK_ID), $singer));
        }

        $singer->update($request->safe()->only(['voice_part_id']));

        return redirect()
            ->route('singers.show', $singer)
            ->with(['status' => 'Voice Placement created. ']);
    }

    public function edit(Membership $singer, Placement $placement, Request $request): View|Response
    {
        $this->authorize('update', $placement);

        $singer->load('user');

        return Inertia::render('Singers/Placements/Edit', [
            'singer' => $singer,
            'placement' => $placement,
            'voice_parts' => VoicePart::all()->prepend(VoicePart::getNullVoicePart())->values(),
        ]);
    }

    public function update(PlacementRequest $request, Membership $singer, Placement $placement): RedirectResponse
    {
        $this->authorize('update', $placement);

        $placement->update($request->safe()->except('voice_part_id'));

        $singer->update($request->safe()->only(['voice_part_id']));

        return redirect()
            ->route('singers.show', $singer)
            ->with(['status' => 'Voice Placement updated.']);
    }
}
