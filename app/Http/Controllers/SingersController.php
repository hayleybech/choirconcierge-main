<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\StoreSinger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Singer;
use App\Models\Task;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SingersController extends Controller
{
    const PROFILE_TASK_ID = 1;
    const PLACEMENT_TASK_ID = 2;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    public function index(Request $request): View
    {
        // Base query
        $singers = Singer::with(['tasks', 'category', 'placement', 'profile'])
            ->filter()
            ->get();

        // Sort
        $sort_by = $request->input('sort_by', 'name');
        $sort_dir = $request->input('sort_dir', 'asc');
        if( $sort_dir === 'asc') {
            $singers = $singers->sortBy($sort_by);
        } else {
            $singers = $singers->sortByDesc($sort_by);
        }

        $sorts = $this->getSorts($request);

        $filters = Singer::getFilters();

        return view('singers.index', compact('singers', 'filters', 'sorts' ));
	}

    public function create(): View
    {
        return view('singers.create');
    }

    public function store(StoreSinger $request): RedirectResponse
    {
        $singer = Singer::create($request->validated());

        return redirect('/singers')->with(['status' => 'Singer created. ', ]);
    }

    public function show(Singer $singer): View
    {
        return view('singers.show', compact('singer'));
    }

    public function edit(Singer $singer): View
    {
        return view('singers.edit', compact('singer' ));
    }
    public function update(Singer $singer, StoreSinger $request): RedirectResponse
    {
        $singer->update($request->validated());

        return redirect()->route('singers.show', [$singer])->with(['status' => 'Singer saved. ']);
    }

    public function delete(Singer $singer): RedirectResponse
    {
        $singer->user->delete();
        $singer->delete();

        return redirect()->route('singers.index')->with(['status' => 'Singer deleted. ', ]);
    }

    public function getSorts(Request $request): array
    {
        $sort_cols = [
            'name',
            'voice_placement.part',
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
	
	public function completeTask(Singer $singer, Task $task): RedirectResponse
    {
        event(new TaskCompleted($task, $singer));

		// Complete type-specific action
		if( $task->type === 'manual' ) {
			// Simply mark as done. 
			$singer->tasks()->updateExistingPivot($task, ['completed' => true]);
			return redirect('/singers')->with(['status' => 'Task updated. ', ]);
		} else {
			// Redirect to form
			// Shouldn't get to this line. Forms tasks skip this entire function.
		}
	}
	
	public function createProfile(Singer $singer): View
    {
		return view('singers.createprofile', compact('singer'));
	}
	
	public function storeProfile(Singer $singer, Request $request): RedirectResponse
    {
		$singer->profile()->create($request->all()); // refer to whitelist in model
		
		if( $singer->onboarding_enabled ) {
            // Mark matching task completed
            //$task = $singer->tasks()->where('name', 'Member Profile')->get();
            $singer->tasks()->updateExistingPivot( self::PROFILE_TASK_ID, array('completed' => true) );

            event( new TaskCompleted(Task::find(self::PROFILE_TASK_ID), $singer) );
        }

		return redirect('/singers')->with(['status' => 'Member Profile created. ', ]);
	}
	
	public function createPlacement(Singer $singer): View
    {
		return view('singers.createplacement', compact('singer'));
	}
	
	public function storePlacement(Singer $singer, Request $request): RedirectResponse
    {
		$singer->placement()->create($request->all()); // refer to whitelist in model
		
		if( $singer->onboarding_enabled ) {
            // Mark matching task completed
            //$task = $singer->tasks()->where('name', 'Voice Placement')->get();
            $singer->tasks()->updateExistingPivot( self::PLACEMENT_TASK_ID, array('completed' => true) );

            event( new TaskCompleted(Task::find(self::PLACEMENT_TASK_ID), $singer) );
        }

		return redirect('/singers')->with(['status' => 'Voice Placement created. ', ]);
	}

	public function move(Singer $singer, Request $request): RedirectResponse
    {
        $category = $request->input('move_category', 0);

        if( $category === 0 ) return redirect('/singers')->with([ 'status' => 'No category selected. ', 'fail' => true ]);

        // Attach to Prospects category
        $singer->category()->associate($category);
        $singer->save();

        return redirect('/singers')->with(['status' => 'The singer was moved. ']);
    }
}
