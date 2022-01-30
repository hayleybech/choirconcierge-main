<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiserStackRequest;
use App\Models\RiserStack;
use App\Models\Singer;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RiserStackController extends Controller
{
	public function index(Request $request): View|Response
	{
		$this->authorize('viewAny', RiserStack::class);

		// Base query
		$stacks = RiserStack::all();

        if(config('features.rebuild')){
            return Inertia::render('RiserStacks/Index', [
                'stacks' => $stacks->values(),
            ]);
        }

		return view('stacks.index', compact('stacks'));
	}

	public function create(): View|Response
	{
		$this->authorize('create', RiserStack::class);

		$voice_parts = VoicePart::with(['singers' => function($query) {
		    $query->active()->with('user');
        }])->get();
		$voice_parts->each(fn($part) => $part->singers->each->append('user_avatar_thumb_url'));

        if(config('features.rebuild')){
            return Inertia::render('RiserStacks/Create', [
                'voice_parts' => $voice_parts->values(),
            ]);
        }

		return view('stacks.create', compact('voice_parts'));
	}

	public function store(RiserStackRequest $request): RedirectResponse
	{
		$this->authorize('create', RiserStack::class);

		$stack = RiserStack::create($request->validated());

		$positions = $this->prepPositions($request);
		$stack->singers()->sync($positions);

		return redirect()
			->route('stacks.show', [$stack])
			->with(['status' => 'Riser stack created. ']);
	}

	public function show(RiserStack $stack): View|Response
	{
		$this->authorize('view', $stack);

		$stack->load('singers.user');
        $stack->singers->each->append('user_avatar_thumb_url');

        $stack->can = [
            'update_stack' => auth()->user()?->can('update', $stack),
            'delete_stack' => auth()->user()?->can('delete', $stack),
        ];

		$voice_parts = VoicePart::with(['singers.user'])->get();
        $voice_parts->each(fn($part) => $part->singers->each->append('user_avatar_thumb_url'));

        if(config('features.rebuild')){
            return Inertia::render('RiserStacks/Show', [
                'stack' => $stack,
            ]);
        }

		return view('stacks.show', compact('stack', 'voice_parts'));
	}

	public function edit(RiserStack $stack): View|Response
	{
		$this->authorize('update', $stack);

		// Get singers that are already on the riser stack.
        $stack->load(['singers' => function($query) {
            $query->active()->with('user');
        }]);
        $stack->singers->each->append('user_avatar_thumb_url');

		// Get singers (by voice part) who are not already on the riser stack.
		$voice_parts = VoicePart::with([
			'singers' => static function ($query) use ($stack) {
				$query->active()->whereDoesntHave('riser_stacks', static function ($query) use ($stack) {
					$query->where('riser_stack_id', '=', $stack->id);
				});
			},
            'singers.user',
		])->get();
        $voice_parts->each(fn($part) => $part->singers->each->append('user_avatar_thumb_url'));

        if(config('features.rebuild')){
            return Inertia::render('RiserStacks/Edit', [
                'stack' => $stack,
                'voice_parts' => $voice_parts->values(),
            ]);
        }

		return view('stacks.edit', compact('stack', 'voice_parts'));
	}

	public function update(RiserStack $stack, RiserStackRequest $request): RedirectResponse
	{
		$this->authorize('update', $stack);

		$stack->update($request->validated());

		$positions = $this->prepPositions($request);
		$stack->singers()->sync($positions);

		return redirect()
			->route('stacks.show', [$stack])
			->with(['status' => 'Riser stack updated. ']);
	}

	public function destroy(RiserStack $stack): RedirectResponse
	{
		$this->authorize('delete', $stack);

		$stack->delete();

		return redirect()
			->route('stacks.index')
			->with(['status' => 'Riser stack deleted. ']);
	}

	/**
	 * Takes the crappy array format I sent the controller from Vue,
	 * and turns it into a format compatible with sync().
	 *
	 * @todo Convert the riser position data within the Vue component.
	 *
	 * @param RiserStackRequest $request
	 *
	 * @return array<array>
	 */
	private function prepPositions(RiserStackRequest $request): array
	{
        if(config('features.rebuild')){
		    $position_data = $request->validated()['singer_positions'];
        } else {
		    $position_data = json_decode($request->validated()['singer_positions'], true);
        }

		return collect($position_data)
            ->mapWithKeys(fn($item) => [
                $item['id'] => [
                    'row' => $item['position']['row'],
                    'column' => $item['position']['column'],
                ]
            ])
            ->all();
	}
}
