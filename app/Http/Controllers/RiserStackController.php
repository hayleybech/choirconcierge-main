<?php

namespace App\Http\Controllers;

use App\Http\Requests\RiserStackRequest;
use App\Models\Ensemble;
use App\Models\Membership;
use App\Models\RiserStack;
use App\Models\VoicePart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RiserStackController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(RiserStack::class, 'stack');
    }

    public function index(Request $request): Response
    {
        $user = auth()->user();
        $canUpdateRiserStacks = $user?->isSuperAdmin || $user?->membership?->hasAbility('riser_stacks_update');

        $ensembles = Ensemble::query()
            ->when(! $canUpdateRiserStacks, function (Builder $query) use ($user) {
                $query->whereIn('id', $user?->membership?->enrolments->pluck('ensemble_id') ?? []);
            })
            ->get();

        $userEnsemblesCount = $canUpdateRiserStacks ? Ensemble::count() : ($user?->membership?->enrolments->count() ?? 0);

        return Inertia::render('RiserStacks/Index', [
            'stacks' => $this->getStacks(),
            'ensembles' => $ensembles,
            'userEnsemblesCount' => $userEnsemblesCount,
        ]);
    }

    private function getStacks(): LengthAwarePaginator
    {
        $user = auth()->user();
        $canUpdateRiserStacks = $user?->isSuperAdmin || $user?->membership?->hasAbility('riser_stacks_update');

        return QueryBuilder::for(RiserStack::class)
            ->with('ensembles')
            ->allowedFilters([
                AllowedFilter::exact('ensembles.id'),
            ])
            ->when(! $canUpdateRiserStacks, function (Builder $query) use ($user) {
                $query->where(function (Builder $query) use ($user) {
                    $query->whereHas('ensembles', function (Builder $query) use ($user) {
                        $query->whereIn('ensembles.id', $user?->membership?->enrolments->pluck('ensemble_id') ?? []);
                    })->orDoesntHave('ensembles');
                });
            })
            ->paginate(20)
            ->appends(request()->query());
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
            'ensembles' => Ensemble::all()->values(),
        ]);
    }

    public function store(RiserStackRequest $request): RedirectResponse
    {
        $stack = RiserStack::create($request->validated());

        $positions = $this->prepPositions($request->validated('singer_positions'));
        $stack->members()->sync($positions);

        $stack->ensembles()->sync($request->validated('ensembles', []));

        return redirect()
            ->route('stacks.show', [$stack])
            ->with(['status' => 'Riser stack created. ']);
    }

    public function show(RiserStack $stack): Response
    {
        $stack->load(['members.user', 'ensembles']);
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
        }, 'members.enrolments', 'ensembles']);
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
            'ensembles' => Ensemble::all()->values(),
        ]);
    }

    public function update(RiserStack $stack, RiserStackRequest $request): RedirectResponse
    {
        $stack->update($request->validated());

        $positions = $this->prepPositions($request->validated('singer_positions'));
        $stack->members()->sync($positions);

        $stack->ensembles()->sync($request->validated('ensembles', []));

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

    public function clone(RiserStack $stack): RedirectResponse
    {
        $this->authorize('create', RiserStack::class);

        $clone = $stack->load('members')
            ->replicate()
            ->fill([
                'title' => 'Copy of ' . $stack->title,
                'created_at' => now(),
                'update_at' => now(),
            ]);

        $clone->save();

        $clone->members()->attach($stack->members
            ->mapWithKeys(fn($member) => [$member->id => [
                'row' => $member->position->row,
                'column' => $member->position->column,
            ]])->all());

        return redirect()
            ->route('stacks.show', [$clone])
            ->with(['status' => 'Riser stack cloned. ']);
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
            ->mapWithKeys(fn($item) => [
                $item['id'] => [
                    'row' => $item['position']['row'],
                    'column' => $item['position']['column'],
                ],
            ])
            ->all();
    }
}
