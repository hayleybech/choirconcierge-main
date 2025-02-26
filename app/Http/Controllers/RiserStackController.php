<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiserStackRequest;
use App\Models\Membership;
use App\Models\RiserStack;
use App\Models\VoicePart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RiserStackController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(RiserStack::class, 'stack');
    }

    public function index(Request $request): Response
    {
        return Inertia::render('RiserStacks/Index', [
            'stacks' => RiserStack::all()->values(),
        ]);
    }

    public function create(): Response
    {
        $singers = Membership::query()
            ->active()
            ->with(['user', 'enrolments'])
            ->get()
            ->append('user_avatar_thumb_url');

        return Inertia::render('RiserStacks/Create', [
            'voiceParts' => VoicePart::all()->values(),
            'singers' => $singers,
        ]);
    }

    public function store(RiserStackRequest $request): RedirectResponse
    {
        $stack = RiserStack::create($request->validated());

        $positions = $this->prepPositions($request->validated('singer_positions'));
        $stack->members()->sync($positions);

        return redirect()
            ->route('stacks.show', [$stack])
            ->with(['status' => 'Riser stack created. ']);
    }

    public function show(RiserStack $stack): Response
    {
        $stack->load('members.user');
        $stack->members->each->append('user_avatar_thumb_url');

        $stack->can = [
            'update_stack' => auth()->user()?->can('update', $stack),
            'delete_stack' => auth()->user()?->can('delete', $stack),
        ];

        return Inertia::render('RiserStacks/Show', [
            'stack' => $stack,
        ]);
    }

    public function edit(RiserStack $stack): Response
    {
        // Get singers that are already on the riser stack.
        $stack->load(['members' => function ($query) {
            $query->active()->with('user');
        }, 'members.enrolments']);
        $stack->members->each->append('user_avatar_thumb_url');

        // Get singers who are not already on the riser stack.
        $singers = Membership::query()
            ->active()
            ->with(['user', 'enrolments'])
            ->whereDoesntHave('riser_stacks', static function ($query) use ($stack) {
                $query->where('riser_stack_id', '=', $stack->id);
            })
            ->get()
            ->append('user_avatar_thumb_url');

        return Inertia::render('RiserStacks/Edit', [
            'stack' => $stack,
            'voiceParts' => VoicePart::all()->values(),
            'singers' => $singers->values(),
        ]);
    }

    public function update(RiserStack $stack, RiserStackRequest $request): RedirectResponse
    {
        $stack->update($request->validated());

        $positions = $this->prepPositions($request->validated('singer_positions'));
        $stack->members()->sync($positions);

        return redirect()
            ->route('stacks.show', [$stack])
            ->with(['status' => 'Riser stack updated. ']);
    }

    public function destroy(RiserStack $stack): RedirectResponse
    {
        $stack->delete();

        return redirect()
            ->route('stacks.index')
            ->with(['status' => 'Riser stack deleted. ']);
    }

    /**
     * Takes the crappy array format I sent the controller from React,
     * and turns it into a format compatible with sync().
     *
     * @todo Convert the riser position data within the React component.
     */
    private function prepPositions(array $singerPositions): array
    {
        return collect($singerPositions)
            ->mapWithKeys(fn ($item) => [
                $item['id'] => [
                    'row' => $item['position']['row'],
                    'column' => $item['position']['column'],
                ],
            ])
            ->all();
    }
}
