<?php

namespace App\Http\Controllers;

use App\CustomSorts\SingerNameSort;
use App\CustomSorts\SingerStatusSort;
use App\CustomSorts\SingerVoicePartSort;
use App\Http\Requests\CreateSingerRequest;
use App\Http\Requests\EditSingerRequest;
use App\Models\CustomField;
use App\Models\Ensemble;
use App\Models\Event;
use App\Models\EventType;
use App\Models\Placement;
use App\Models\Role;
use App\Models\Membership;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Mailgun\Mailgun;
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

        $pagination = $this->getSingers($defaultStatus);
        return Inertia::render('Singers/Index', [
            'allSingers' => $pagination->getCollection()->append('fee_status'),
            'pagination' => $pagination,
            'statuses' => SingerCategory::all()->values(),
            'defaultStatus' => $defaultStatus,
            'voiceParts' => VoicePart::all()->values(),
            'roles' => Role::all()->values(),
            'ensembles' => Ensemble::all()->values(),
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
                'user_roles',
            ])
        );
        $singer->initOnboarding();
        $singer->save();

        $adminRoleId = Role::where('name', 'Admin')->value('id');
        if (!$singer->hasRole('Admin') && Arr::has($request->validated('user_roles', []), $adminRoleId)) {
            $singer->user->subscribeToAlerts();
        }

        User::sendWelcomeEmail($user);

        return redirect()
            ->route('singers.show', [$singer])
            ->with(['status' => 'Singer created. ']);
    }

    public function show(Membership $singer): InertiaResponse
    {
        $singer->append('fee_status');

        $singer->load([
            'user',
            'enrolments' => ['voice_part', 'ensemble'],
            'category',
            'roles',
            'placement',
            'tasks',
        ]);

        $singer->can = [
            'update_singer' => auth()->user()?->can('update', $singer),
            'delete_singer' => auth()->user()?->can('delete', $singer),
            'create_placement' => auth()->user()?->can('create', [Placement::class, $singer]),
            'view_attendance' => auth()->user()?->can('viewAttendance', $singer),
        ];

        $singer->tasks->each(fn($task) => $task->can = ['complete' => auth()->user()?->can('complete', $task)]);

        return Inertia::render('Singers/Show', [
            'singer' => $singer,
            'attendance_summary' => self::getAttendanceSummary($singer),
            'categories' => SingerCategory::all(),
            'voiceParts' => VoicePart::all(),
            'customFields' => CustomField::query()
                ->with('entries', fn($query) => $query->where('membership_id', $singer->id))
                ->get(),
            'ensemblesNotEnrolled' => Ensemble::whereDoesntHave('enrolments', fn(Builder $query) => $query->where('membership_id', $singer->id)
            )->get(),
        ]);
    }

    private static function getAttendanceSummary(Membership $singer): array
    {
        if (! auth()->user()?->can('viewAttendance', $singer)) {
            return [];
        }

        $eightWeeksAgo = now()->subWeeks(8);
        $rehearsalType = EventType::where('title', 'Rehearsal')->first();
        if (!$rehearsalType) {
            return [];
        }

        $recentRehearsalsCount = Event::where('type_id', $rehearsalType->id)
            ->where('start_date', '>=', $eightWeeksAgo)
            ->where('start_date', '<=', now())
            ->count();

        $attendedCount = $singer->attendances()
            ->whereIn('response', ['present', 'late'])
            ->whereHas('event', function ($query) use ($eightWeeksAgo, $rehearsalType) {
                $query->where('type_id', $rehearsalType->id)
                    ->where('start_date', '>=', $eightWeeksAgo)
                    ->where('start_date', '<=', now());
            })
            ->count();

        return [
            'rehearsals_last_8_weeks' => [
                'attended' => $attendedCount,
                'total' => $recentRehearsalsCount,
                'percentage' => $recentRehearsalsCount > 0 ? round(($attendedCount / $recentRehearsalsCount) * 100) : 0,
            ],
        ];
    }

    public function edit(Membership $singer): InertiaResponse
    {
        $singer->load('user', 'category', 'roles');

        return Inertia::render('Singers/Edit', [
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
            'singer' => $singer,
        ]);
    }

    public function update(Membership $singer, EditSingerRequest $request): RedirectResponse
    {
        $adminRoleId = Role::where('name', 'Admin')->value('id');
        if ($singer->hasRole('Admin') && !Arr::has($request->validated('user_roles', []), $adminRoleId)) {
            $singer->user->unsubscribeFromAlerts();
        }
        if (!$singer->hasRole('Admin') && Arr::has($request->validated('user_roles', []), $adminRoleId)) {
            $singer->user->subscribeToAlerts();
        }

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

    private function getSingers(string $defaultStatus): LengthAwarePaginator
    {
        $nameSort = AllowedSort::custom('full-name', new SingerNameSort(), 'name');

        return QueryBuilder::for(Membership::class)
            ->with(['tasks', 'category', 'user', 'enrolments' => ['voice_part', 'ensemble'],])
            ->allowedFilters([
                AllowedFilter::callback('user.name', fn(Builder $query, $value) => $query
                    ->whereHas('user', fn(Builder $query) => $query
                        ->whereRaw('CONCAT(first_name, ?, last_name) LIKE LOWER(?)', [' ', "%$value%"])
                        ->orWhereRaw('email LIKE LOWER(?)', ["%$value%"])
                    )),
                AllowedFilter::exact('category.id')
                    ->default([$defaultStatus]),
                AllowedFilter::callback('enrolments.voice_part_id', fn(Builder $query, $value) => $query
                    ->whereHas('enrolments', fn(Builder $query) => $query
                        ->where('voice_part_id', '=', $value)
                    )
                ),
                AllowedFilter::callback('enrolments.ensemble_id', fn(Builder $query, $value) => $query
                    ->whereHas('enrolments', fn(Builder $query) => $query
                        ->where('ensemble_id', '=', $value)
                    )
                ),
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
            ->paginate(50)->appends(request()->query());
    }

    private function maybeCreateUser(CreateSingerRequest $request): Builder|Model
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
