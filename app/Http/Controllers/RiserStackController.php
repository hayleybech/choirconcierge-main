<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiserStackRequest;
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
        // store members directly on voice parts, but filter by enrolment
        $voice_parts = VoicePart::with(['enrolments' => function ($query) {
                $query->whereHas('membership', function($query) {
                    $query->active()->with('user');
                });
            }, 'enrolments.membership.user'])
            ->get()
            ->each(function($part) {
                $part->members = $part->enrolments->map(function ($enrolment) {
                    $membership = $enrolment->membership;
                    $membership->voice_part_id = $enrolment->voice_part_id;
                    $membership->append('user_avatar_thumb_url');
                    return $enrolment->membership;
                });
            });

        return Inertia::render('RiserStacks/Create', [
            'voice_parts' => $voice_parts->values(),
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
        }]);
        $stack->members->each->append('user_avatar_thumb_url');

        // Get singers (by voice part) who are not already on the riser stack.
        $voice_parts = VoicePart::with(['enrolments' => function ($query) use ($stack) {
                $query->whereHas('membership', function($query) use ($stack) {
                    $query->active()
                        ->with('user')
                        ->whereDoesntHave('riser_stacks', static function ($query) use ($stack) {
                            $query->where('riser_stack_id', '=', $stack->id);
                        });
                });
            }, 'enrolments.membership.user'])
            ->get()
            ->each(function($part) {
                $part->members = $part->enrolments->map(function ($enrolment) {
                    $membership = $enrolment->membership;
                    $membership->voice_part_id = $enrolment->voice_part_id;
                    $membership->append('user_avatar_thumb_url');
                    return $enrolment->membership;
                });
            });

        return Inertia::render('RiserStacks/Edit', [
            'stack' => $stack,
            'voice_parts' => $voice_parts->values(),
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
