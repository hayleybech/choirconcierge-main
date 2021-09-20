<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\PlacementRequest;
use App\Models\Placement;
use App\Models\Singer;
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

	public function create(Singer $singer): View
	{
	    $this->authorize('create', [Placement::class, $singer]);

		$voice_parts =
			[0 => 'None'] +
			VoicePart::all()
				->pluck('title', 'id')
				->toArray();

		return view('singers.createplacement', compact('singer', 'voice_parts'));
	}

	public function store(Singer $singer, PlacementRequest $request): RedirectResponse
	{
        $this->authorize('create', [Placement::class, $singer]);

		$singer->placement()->create($request->validated()); // refer to whitelist in model

		if ($singer->onboarding_enabled) {
			// Mark matching task completed
			//$task = $singer->tasks()->where('name', 'Voice Placement')->get();
			$singer->tasks()->updateExistingPivot(self::PLACEMENT_TASK_ID, ['completed' => true]);

			event(new TaskCompleted(Task::find(self::PLACEMENT_TASK_ID), $singer));
		}

		$singer->update([
			'voice_part_id' => $request->validated()['voice_part_id'],
		]);

		return redirect()
			->route('singers.show', $singer)
			->with(['status' => 'Voice Placement created. ']);
	}

	public function edit(Singer $singer, Placement $placement, Request $request): View|Response
	{
        $this->authorize('update', $placement);

        $singer->load('user');

        $voice_parts = VoicePart::all()->prepend(VoicePart::getNullVoicePart());

        if(config('features.rebuild')) {
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Singers/Placements/Edit', [
                'singer' => $singer,
                'placement' => $placement,
                'voice_parts' => $voice_parts->values(),
            ]);
        }

		return view('singers.editplacement', compact('singer', 'placement', 'voice_parts'));
	}

	public function update(PlacementRequest $request, Singer $singer, Placement $placement): RedirectResponse
	{
        $this->authorize('update', $placement);

		$placement->update($request->validated());

		$singer->update([
			'voice_part_id' => $request->validated()['voice_part_id'],
		]);

		return redirect()
			->route('singers.show', $singer)
			->with(['status' => 'Voice Placement updated.']);
	}
}
