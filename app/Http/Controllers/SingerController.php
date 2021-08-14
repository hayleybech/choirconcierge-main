<?php

namespace App\Http\Controllers;

use App\Http\Requests\SingerRequest;
use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Singer;
use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

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

	public function index(Request $request): View|Response
	{
		$this->authorize('viewAny', Singer::class);

		// Base query
		$all_singers = Singer::with(['tasks', 'category', 'voice_part', 'user'])
			->filter()
			->get();

		// Sort
		$sort_by = $request->input('sort_by', 'name');
		$sort_dir = $request->input('sort_dir', 'asc');
		if ($sort_dir === 'asc') {
			$all_singers = $all_singers->sortBy($sort_by);
		} else {
			$all_singers = $all_singers->sortByDesc($sort_by);
		}

        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Singers/Index', [
                'all_singers' => $all_singers,
                'active_singers' => $all_singers->whereIn('category.name', ['Members', 'Prospects']),
                'member_singers' => $all_singers->whereIn('category.name', ['Members']),
                'prospect_singers' => $all_singers->whereIn('category.name', ['Prospects']),
                'archived_singers' => $all_singers->whereIn('category.name', ['Archived Members', 'Archived Prospects']),
                'filters' => Singer::getFilters(),
                'sorts' => ($sorts = $this->getSorts($request)),
                'categories' => SingerCategory::all(),
            ]);
        }

		return view('singers.index', [
			'all_singers' => $all_singers,
			'active_singers' => $all_singers->whereIn('category.name', ['Members', 'Prospects']),
			'member_singers' => $all_singers->whereIn('category.name', ['Members']),
			'prospect_singers' => $all_singers->whereIn('category.name', ['Prospects']),
			'archived_singers' => $all_singers->whereIn('category.name', ['Archived Members', 'Archived Prospects']),
			'filters' => Singer::getFilters(),
			'sorts' => ($sorts = $this->getSorts($request)),
			'categories' => SingerCategory::all(),
		]);
	}

	public function create(): View
	{
		$this->authorize('create', Singer::class);

		$voice_parts =
			[0 => 'None'] +
			VoicePart::all()
				->pluck('title', 'id')
				->toArray();
		$roles = Role::all();

		return view('singers.create', compact('voice_parts', 'roles'));
	}

	public function store(UserRequest $request): RedirectResponse
	{
		$this->authorize('create', Singer::class);

        $user = User::create(Arr::except($request->validated(), [
            'onboarding_enabled',
            'reason_for_joining',
            'referrer',
            'membership_details',
            'joined_at',
            'voice_part_id',
            'password_confirmation',
        ]));
        $singer = $user->singers()->create(Arr::only($request->validated(), [
            'onboarding_enabled',
            'reason_for_joining',
            'referrer',
            'membership_details',
            'joined_at',
            'voice_part_id',
        ]));
        $singer->initOnboarding();
        $singer->save();

		User::sendWelcomeEmail($user);

		return redirect()
			->route('singers.show', [$singer])
			->with(['status' => 'Singer created. ']);
	}

	public function show(Singer $singer): View|Response
	{
		$this->authorize('view', $singer);

		$singer->load('user');

        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Singers/Show', [
                'singer' => $singer,
                'categories' => SingerCategory::all(),
            ]);
        }

        return view('singers.show', [
			'singer' => $singer,
			'categories' => SingerCategory::all(),
		]);
	}

	public function edit(Singer $singer): View
	{
		$this->authorize('update', $singer);

		$voice_parts =
			[0 => 'None'] +
			VoicePart::all()
				->pluck('title', 'id')
				->toArray();

		$roles = Role::all();

		return view('singers.edit', compact('singer', 'voice_parts', 'roles'));
	}
	public function update(Singer $singer, SingerRequest $request): RedirectResponse
	{
		$this->authorize('update', $singer);

        $singer->update(Arr::only($request->validated(), [
            'reason_for_joining',
            'referrer',
            'membership_details',
            'joined_at',
            'onboarding_enabled',
            'voice_part_id',
        ]));
        $singer->update(Arr::only($request->validated(), [
            'user_roles'
        ]));

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
		$url = $request->url() . '?' . Singer::getFilterQueryString();

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
				'url' => $url . "&sort_by=$col&sort_dir=$dir",
				'dir' => $current_dir,
				'current' => $current,
			];
		}
		return $sorts;
	}
}
