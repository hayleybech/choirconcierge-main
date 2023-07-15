<?php

namespace App\Http\Controllers;

use App\CustomSorts\SingerNameSort;
use App\CustomSorts\SingerStatusSort;
use App\CustomSorts\SingerVoicePartSort;
use App\Http\Requests\CreateSingerRequest;
use App\Http\Requests\EditSingerRequest;
use App\Models\Placement;
use App\Models\Role;
use App\Models\Membership;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class SingerController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Membership::class, 'singer');
    }

    public function index(Request $request): InertiaResponse
    {
        $defaultStatus = SingerCategory::all()->firstWhere('name', 'Members')->id;

        return Inertia::render('Singers/Index', [
            'allSingers' => $this->getSingers($defaultStatus)->values(),
            'statuses' => SingerCategory::all()->values(),
            'defaultStatus' => $defaultStatus,
            'voiceParts' => VoicePart::all()->values(),
            'roles' => Role::all()->values(),
        ]);
    }

    public function create(): InertiaResponse
    {
        return Inertia::render('Singers/Create', [
            'voice_parts' => VoicePart::all()->prepend(VoicePart::getNullVoicePart())->values(),
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
        ]);
    }

    public function store(CreateSingerRequest $request): RedirectResponse
    {
        $user = $this->maybeCreateUser($request);

        $singer = Membership::create($request->safe()
            ->merge(['user_id' => $user->id])
            ->only([
                'user_id',
                'onboarding_enabled',
                'reason_for_joining',
                'referrer',
                'membership_details',
                'joined_at',
                'voice_part_id',
                'user_roles',
            ])
        );
        $singer->initOnboarding();
        $singer->save();

        User::sendWelcomeEmail($user);

        return redirect()
            ->route('singers.show', [$singer])
            ->with(['status' => 'Singer created. ']);
    }

    public function show(Membership $singer): InertiaResponse
    {
		$singer->append('fee_status');

        $singer->load('user', 'voice_part', 'category', 'roles', 'placement', 'tasks');

        $singer->can = [
            'update_singer' => auth()->user()?->can('update', $singer),
            'delete_singer' => auth()->user()?->can('delete', $singer),
            'create_placement' => auth()->user()?->can('create', [Placement::class, $singer]),
        ];
        $singer->tasks->each(fn ($task) => $task->can = ['complete' => auth()->user()?->can('complete', $task)]);

        return Inertia::render('Singers/Show', [
            'singer' => $singer,
            'categories' => SingerCategory::all(),
        ]);
    }

    public function edit(Membership $singer): InertiaResponse
    {
        $singer->load('user', 'voice_part', 'category', 'roles');

        return Inertia::render('Singers/Edit', [
            'voice_parts' => VoicePart::all()->prepend(VoicePart::getNullVoicePart())->values(),
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
            'singer' => $singer,
        ]);
    }

    public function update(Membership $singer, EditSingerRequest $request): RedirectResponse
    {
        $singer->update($request->safe()
            ->merge(['user_roles' => array_merge(
                $request->validated('user_roles', []),
                [Role::where('name', '=', 'User')->pluck('id')->first()]
            )])
            ->only([
                'user_roles',
                'reason_for_joining',
                'referrer',
                'membership_details',
                'joined_at',
                'onboarding_enabled',
                'voice_part_id',
                'paid_until',
            ])
        );

        return redirect()
            ->route('singers.show', [$singer])
            ->with(['status' => 'Singer saved. ']);
    }

    public function destroy(Membership $singer): RedirectResponse
    {
        $singer->delete();

        return redirect()
            ->route('singers.index')
            ->with(['status' => 'Singer deleted. ']);
    }

    private function getSingers(string $defaultStatus): Collection
    {
        $nameSort = AllowedSort::custom('full-name', new SingerNameSort(), 'name');

        return QueryBuilder::for(Membership::class)
            ->with(['tasks', 'category', 'voice_part', 'user'])
            ->allowedFilters([
                AllowedFilter::callback('user.name', fn(Builder $query, $value) => $query
                    ->whereHas('user', fn(Builder $query) => $query
                        ->whereRaw('CONCAT(first_name, ?, last_name) LIKE ?', [' ', "%$value%"])
                    )),
                AllowedFilter::exact('category.id')
                    ->default([$defaultStatus]),
                AllowedFilter::exact('voice_part.id'),
                AllowedFilter::exact('roles.id'),
                AllowedFilter::callback('fee_status', fn(Builder $query, $value) => match ($value) {
                    'unknown' => $query->whereNull('paid_until'),
                    'expired' => $query->whereDate('paid_until', '<', now()),
                    'expires-soon' => $query->whereDate('paid_until', '>', now())
                        ->whereDate('paid_until', '<', now()->addMonth()),
                    default => $query->whereDate('paid_until', '>', now()->addMonth()),
                })
            ])
            ->allowedSorts([
                $nameSort,
                AllowedSort::custom('status-title', new SingerStatusSort(), 'status'),
                AllowedSort::custom('part-title', new SingerVoicePartSort(), 'part'),
                AllowedSort::field('paid_until'),
            ])
            ->defaultSort($nameSort)
            ->get()
            ->append('fee_status');
    }

    private function maybeCreateUser(CreateSingerRequest $request): Builder|\Illuminate\Database\Eloquent\Model
    {
        if ($request->has('user_id') && !empty($request->input('user_id'))) {
            return User::find($request->input('user_id'));
        }
        return User::create(Arr::only($request->validated(), [
            'email',
            'first_name',
            'last_name',
            'password',
        ]));
    }
}
