<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\SingerRequest;
use App\Models\Role;
use App\Models\User;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Singer;
use App\Models\Task;
use Illuminate\Validation\Rule;

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


    public function index(Request $request): View
    {
        $this->authorize('viewAny', Singer::class);

        // Base query
        $all_singers = Singer::with(['tasks', 'category', 'profile', 'voice_part'])
            ->filter()
            ->get();

        // Sort
        $sort_by = $request->input('sort_by', 'name');
        $sort_dir = $request->input('sort_dir', 'asc');
        if( $sort_dir === 'asc') {
            $all_singers = $all_singers->sortBy($sort_by);
        } else {
            $all_singers = $all_singers->sortByDesc($sort_by);
        }

        return view('singers.index', [
            'all_singers'      => $all_singers,
            'active_singers'   => $all_singers->whereIn('category.name', ['Members', 'Prospects']),
            'member_singers'   => $all_singers->whereIn('category.name', ['Members']),
            'prospect_singers' => $all_singers->whereIn('category.name', ['Prospects']),
            'archived_singers' => $all_singers->whereIn('category.name', ['Archived Members', 'Archived Prospects']),
            'filters'          => Singer::getFilters(),
            'sorts'            => $sorts = $this->getSorts($request),
        ]);
	}

    public function create(): View
    {
        $this->authorize('create', Singer::class);

        $voice_parts = [0 => "None"] + VoicePart::all()->pluck('title', 'id')->toArray();
        $roles = Role::all();

        return view('singers.create', compact('voice_parts', 'roles'));
    }

    public function store(SingerRequest $request): RedirectResponse
    {
        $this->authorize('create', Singer::class);

        $singer = Singer::create($request->validated());
        User::sendWelcomeEmail($singer->user);

        return redirect()->route('singers.show', [$singer])->with(['status' => 'Singer created. ', ]);
    }

    public function show(Singer $singer): View
    {
        $this->authorize('view', $singer);

        return view('singers.show', compact('singer'));
    }

    public function edit(Singer $singer): View
    {
        $this->authorize('update', $singer);

        $voice_parts = [0 => "None"] + VoicePart::all()->pluck('title', 'id')->toArray();

        $roles = Role::all();

        return view('singers.edit', compact('singer', 'voice_parts', 'roles' ));
    }
    public function update(Singer $singer, SingerRequest $request): RedirectResponse
    {
        $this->authorize('update', $singer);

        $singer->update($request->validated());

        return redirect()->route('singers.show', [$singer])->with(['status' => 'Singer saved. ']);
    }

    public function destroy(Singer $singer): RedirectResponse
    {
        $this->authorize('delete', $singer);

        $singer->user->delete();
        $singer->delete();

        return redirect()->route('singers.index')->with(['status' => 'Singer deleted. ', ]);
    }

    public function getSorts(Request $request): array
    {
        $sort_cols = [
            'name',
            'voice_part',
            'category.name',
        ];

        // Merge filters with sort query string
        $url = $request->url() . '?' . Singer::getFilterQueryString();

        $current_sort = $request->input('sort_by', 'name');
        $current_dir =  $request->input('sort_dir', 'asc');

        $sorts = [];
        foreach($sort_cols as $col) {
            // If current sort
            if( $col === $current_sort ) {
                // Create link for opposite sort direction
                $current = true;
                $dir = ( 'asc' === $current_dir ) ? 'desc' : 'asc';
            } else {
                $current = false;
                $dir = 'asc';
            };
            $sorts[$col] = [
                'url'       => $url . "&sort_by=$col&sort_dir=$dir",
                'dir'       => $current_dir,
                'current'   => $current,
            ];
        }
        return $sorts;
    }
}
