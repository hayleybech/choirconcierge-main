<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\PlacementRequest;
use App\Models\Placement;
use App\Models\Membership;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SingerPlacementController extends Controller
{
    public function create(Membership $singer): View|Response
    {
        $this->authorize('create', [Placement::class, $singer]);

        $singer->load('user');

        return Inertia::render('Singers/Placements/Create', [
            'singer' => $singer,
        ]);
    }

    public function store(Membership $singer, PlacementRequest $request): RedirectResponse
    {
        $this->authorize('create', [Placement::class, $singer]);

        $singer->placement()->create($request->validated());

        if ($singer->onboarding_enabled) {
            // Mark matching task completed
            $placement_task = Task::query()->firstWhere('name', '=', 'Voice Placement');

            if($placement_task) {
                $singer->tasks()->updateExistingPivot($placement_task->id, ['completed' => true]);

                event(new TaskCompleted($placement_task, $singer));
            }
        }

        return redirect()
            ->route('singers.show', $singer)
            ->with(['status' => 'Voice Placement created. ']);
    }

    public function edit(Membership $singer, Placement $placement): View|Response
    {
        $this->authorize('update', $placement);

        $singer->load('user');

        return Inertia::render('Singers/Placements/Edit', [
            'singer' => $singer,
            'placement' => $placement,
        ]);
    }

    public function update(PlacementRequest $request, Membership $singer, Placement $placement): RedirectResponse
    {
        $this->authorize('update', $placement);

        $placement->update($request->validated());

        return redirect()
            ->route('singers.show', $singer)
            ->with(['status' => 'Voice Placement updated.']);
    }
}
