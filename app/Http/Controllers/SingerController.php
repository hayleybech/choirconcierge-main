<?php

namespace App\Http\Controllers;

use App\CustomSorts\SingerNameSort;
use App\CustomSorts\SingerStatusSort;
use App\CustomSorts\SingerVoicePartSort;
use App\Http\Requests\CreateSingerRequest;
use App\Http\Requests\EditSingerRequest;
use App\Models\Placement;
use App\Models\Role;
use App\Models\Singer;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index(Request $request): View|InertiaResponse
    {
        $this->authorize('viewAny', Singer::class);

        $nameSort = AllowedSort::custom('full-name', new SingerNameSort(), 'name');

        $statuses = SingerCategory::all();
        $defaultStatus = $statuses->firstWhere('name', 'Members')->id;

        $allSingers = QueryBuilder::for(Singer::class)
            ->with(['tasks', 'category', 'voice_part', 'user'])
            ->allowedFilters([
                AllowedFilter::callback('user.name', fn (Builder $query, $value) => $query
                    ->whereHas('user', fn (Builder $query) => $query
                        ->whereRaw('CONCAT(first_name, ?, last_name) LIKE ?', [' ', "%$value%"])
                )),
                AllowedFilter::exact('category.id')
                    ->default([$defaultStatus]),
                AllowedFilter::exact('voice_part.id'),
                AllowedFilter::exact('roles.id'),
            ])
            ->allowedSorts([
                $nameSort,
                AllowedSort::custom('status-title', new SingerStatusSort(), 'status'),
                AllowedSort::custom('part-title', new SingerVoicePartSort(), 'part'),
            ])
            ->defaultSort($nameSort)
            ->get();

        return Inertia::render('Singers/Index', [
            'allSingers' => $allSingers->values(),
            'statuses' => $statuses->values(),
            'defaultStatus' => $defaultStatus,
            'voiceParts' => VoicePart::all()->values(),
            'roles' => Role::all()->values(),
        ]);
    }

    public function create(): View|InertiaResponse
    {
        $this->authorize('create', Singer::class);

        $voice_parts = VoicePart::all()->prepend(VoicePart::getNullVoicePart());
        $roles = Role::where('name', '!=', 'User')->get();

        return Inertia::render('Singers/Create', [
            'voice_parts' => $voice_parts->values(),
            'roles' => $roles->values(),
        ]);
    }

    public function store(CreateSingerRequest $request): RedirectResponse
    {
        $this->authorize('create', Singer::class);

        if ($request->has('user_id') && ! empty($request->input('user_id'))) {
            $user = User::find($request->input('user_id'));
        } else {
            $user = User::create(Arr::only($request->validated(), [
                'email',
                'first_name',
                'last_name',
                'password',
            ]));
        }
        $singer = Singer::create(array_merge(
            ['user_id' => $user->id],
            $request->only([
                'onboarding_enabled',
                'reason_for_joining',
                'referrer',
                'membership_details',
                'joined_at',
                'voice_part_id',
                'user_roles',
            ])
        ));
        $singer->initOnboarding();
        $singer->save();

        User::sendWelcomeEmail($user);

        return redirect()
            ->route('singers.show', [$singer])
            ->with(['status' => 'Singer created. ']);
    }

    public function show(Singer $singer): View|InertiaResponse
    {
        $this->authorize('view', $singer);

		$singer->append('is_paid');

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

    public function edit(Singer $singer): View|InertiaResponse
    {
        $this->authorize('update', $singer);

        $singer->load('user', 'voice_part', 'category', 'roles');

        $voice_parts = VoicePart::all()->prepend(VoicePart::getNullVoicePart());

        $roles = Role::where('name', '!=', 'User')->get();

        return Inertia::render('Singers/Edit', [
            'voice_parts' => $voice_parts->values(),
            'roles' => $roles->values(),
            'singer' => $singer,
        ]);
    }

    public function update(Singer $singer, EditSingerRequest $request): RedirectResponse
    {
        $this->authorize('update', $singer);

        $singer->update(Arr::only($request->validated(), [
            'reason_for_joining',
            'referrer',
            'membership_details',
            'joined_at',
            'onboarding_enabled',
            'voice_part_id',
	        'membership_expires_at',
        ]));
        $singer->update([
            'user_roles' => array_merge(
                $request->validated()['user_roles'] ?? [],
                [Role::where('name', '=', 'User')->pluck('id')->first()]
            ),
        ]);

        return redirect()
            ->route('singers.show', [$singer])
            ->with(['status' => 'Singer saved. ']);
    }

    public function destroy(Singer $singer): RedirectResponse
    {
        $this->authorize('delete', $singer);

        $singer->user->delete();
        $singer->delete();

        return redirect()
            ->route('singers.index')
            ->with(['status' => 'Singer deleted. ']);
    }

    public function getSorts(Request $request): array
    {
        $sort_cols = ['name', 'voice_part', 'category.name'];

        // Merge filters with sort query string
        $url = $request->url().'?'.Singer::getFilterQueryString();

        $current_sort = $request->input('sort_by', 'name');
        $current_dir = $request->input('sort_dir', 'asc');

        $sorts = [];
        foreach ($sort_cols as $col) {
            // If current sort
            if ($col === $current_sort) {
                // Create link for opposite sort direction
                $current = true;
                $dir = 'asc' === $current_dir ? 'desc' : 'asc';
            } else {
                $current = false;
                $dir = 'asc';
            }
            $sorts[$col] = [
                'url' => $url."&sort_by=$col&sort_dir=$dir",
                'dir' => $current_dir,
                'current' => $current,
            ];
        }

        return $sorts;
    }
}
