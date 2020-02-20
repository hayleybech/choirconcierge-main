<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Imports\DripSingersImport;
use App\Libraries\Drip\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Singer;
use App\Task;
use App\SingerCategory;
use App\Libraries\Drip\Drip;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

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
        $singers = Singer::with(['tasks', 'category', 'placement', 'profile']);

        // Filter singers
        $where = [];
        $filters = $this->getFilters($request);

        if($filters['cat']['current'] !== 0) {
            $where[] = ['singer_category_id', '=', $filters['cat']['current']];
        }
        $singers = $singers->where($where);

        if($filters['part']['current'] !== 'all') {
            $current = $filters['part']['current'];
            $singers = $singers->whereHas('placement', function($query) use($current) {
                $query->where('voice_part', '=', $current);
            });
        }

        $adult_age = 18;
        if($filters['age']['current'] === 'adult') {
            $singers = $singers->whereHas('profile', function($query) use($adult_age) {
                $query->where('dob', '<=', date('Y-m-d', strtotime("-$adult_age years")));
            });
        }
        elseif($filters['age']['current'] === 'child') {
            $singers = $singers->whereHas('profile', function($query) use($adult_age) {
                $query->where('dob', '>', date('Y-m-d', strtotime("-$adult_age years")));
            });
        }

        // Finish and fetch
		$singers = $singers->get();

        // Sort
        $sort_by = $request->input('sort_by', 'name');
        $sort_dir = $request->input('sort_dir', 'asc');
        if( $sort_dir === 'asc') {
            $singers = $singers->sortBy($sort_by);
        } else {
            $singers = $singers->sortByDesc($sort_by);
        }

        $sorts = $this->getSorts($request);

        return view('singers.index', compact('singers', 'filters', 'sorts' ));
	}

	public function getFilters(Request $request): array
    {
        return [
            'cat'   => $this->getFilterCategory($request),
            'part'  => $this->getFilterPart($request),
            'age'   => $this->getFilterAge($request),
        ];
    }

    /**
     * Get list of categories for filtering
     *
     * @param Request $request
     *
     * @return array
     */
	public function getFilterCategory(Request $request): array
    {
        $default = 1;

        $categories = SingerCategory::all();
        $categories_keyed = $categories->mapWithKeys(function($item){
            return [ $item['id'] => $item['name'] ];
        });
        $categories_keyed->prepend('All Singers',0);

        return [
            'name'      => 'filter_category',
            'label'     => 'Category',
            'default'   => $default,
            'current'   => $request->input('filter_category', $default),
            'list'      => $categories_keyed,
        ];
    }

    /**
     * Get list of voice parts for filtering
     *
     * @param Request $request
     *
     * @return array
     */
    public function getFilterPart(Request $request): array
    {
        $default = 'all';

        return [
            'name'      => 'filter_part',
            'label'     => 'Part',
            'default'   => $default,
            'current'   => $request->input('filter_part', $default),
            'list'      => [
                'all'   => 'All parts',
                'tenor' => 'Tenor',
                'lead'  => 'Lead',
                'bari'  => 'Baritone',
                'bass'  => 'Bass',
            ],
        ];
    }

    /**
     * Get list of age ranges for filtering
     *
     * @param Request $request
     *
     * @return array
     */
    public function getFilterAge(Request $request): array
    {
        $default = 'all';
        return [
            'name'      => 'filter_age',
            'label'     => 'Age',
            'default'   => $default,
            'current'   => $request->input('filter_age', $default),
            'list'      => [
                'all'    => 'All ages',
                'adult'  => 'Over 18',
                'child'  => 'Under 18',
            ],
        ];
    }

    public function getSorts(Request $request): array
    {
        $sort_cols = [
            'name',
            'voice_placement.part',
            'category.name',
        ];

        // get URL ready
        $url = $request->url() . '?';
        $filters = $this->getFilters($request);
        foreach( $filters as $key => $filter ) {
            $url .= $filter['name'] . '=' . $filter['current'];
            if ( ! $key === array_key_last($filters) ) $url .= '&';
        }
        //print_r($filters);
         //die();

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
	
		public function getSingersByTag($tag): Response
        {
			$Drip = new Drip( config('app.drip_token'), config('app.drip_account')); 
		
			// Get subscribers
			$args = array(
				'tags' => $tag,
			);
			
			$Response = $Drip->get('subscribers', $args);
					
			/*
			if( isset($Reponse->error) ) {
				return view('singers', compact('Response'));
			}*/
			
			return $Response;	
		}
	
		public function getProspects(): Response
        {
			return self::getSingersByTag('Prospective Member');
		}
		
		public function getMembersPaid(): Response
        {
			return self::getSingersByTag('Waiting for Account Creation');
		}
	
	public function create(): View
    {
		return view('singers.create');
	}
	
	public function store(Request $request): RedirectResponse
    {
		$validated = $request->validate([
			'name'	=> 'required',
			'email'	=> 'required|unique:singers',
		]);
		
		$singer = new Singer();
		
		$singer->name  = $request->name;
		$singer->email = $request->email;
		
		$singer->save();
		
		// Attach all tasks
		$tasks = Task::all();
		$singer->tasks()->attach( $tasks );

		// Attach to Prospects category
        $cat_prospects = SingerCategory::find(1);
        $singer->category()->associate($cat_prospects);

        $singer->save();
		
		// Exit
		return redirect('/singers')->with(['status' => 'Singer created. ', ]);
	}

    public function delete($singerId): RedirectResponse
    {
        $singer = Singer::find($singerId);

        $singer->delete();

        return redirect()->route('singers.index')->with(['status' => 'Singer deleted. ', ]);
    }
	
	public function completeTask($singerId, $taskId): RedirectResponse
    {
		$singer = Singer::find($singerId);
		$task = Task::find($taskId);

        event(new TaskCompleted($task, $singer));

		// Complete type-specific action
		if( $task->type === 'manual' ) {
			// Simply mark as done. 
			$singer->tasks()->updateExistingPivot($taskId, ['completed' => true]);
			return redirect('/singers')->with(['status' => 'Task updated. ', ]);
		} else {
			// Redirect to form
			// Shouldn't get to this line. Forms tasks skip this entire function.
		}
	}
	
	public function createProfile($singerId): View
    {
		$singer = Singer::find($singerId);
		
		return view('singers.createprofile', compact('singer'));
	}
	
	public function storeProfile(Request $request): RedirectResponse
    {
		$singer = Singer::find($request->singer_id);
		$singer->profile()->create($request->all()); // refer to whitelist in model
		
		// Mark matching task completed
		//$task = $singer->tasks()->where('name', 'Member Profile')->get();
		$singer->tasks()->updateExistingPivot( self::PROFILE_TASK_ID, array('completed' => true) );

        event( new TaskCompleted(Task::find(self::PROFILE_TASK_ID), $singer) );
		
		return redirect('/singers')->with(['status' => 'Member Profile created. ', ]);
	}
	
	public function createPlacement($singerId): View
    {
		$singer = Singer::find($singerId);
		
		return view('singers.createplacement', compact('singer'));
	}
	
	public function storePlacement(Request $request): RedirectResponse
    {
		$singer = Singer::find($request->singer_id);
		$singer->placement()->create($request->all()); // refer to whitelist in model
		
		// Mark matching task completed
		//$task = $singer->tasks()->where('name', 'Voice Placement')->get();
		$singer->tasks()->updateExistingPivot( self::PLACEMENT_TASK_ID, array('completed' => true) );

        event( new TaskCompleted(Task::find(self::PLACEMENT_TASK_ID), $singer) );
		
		return redirect('/singers')->with(['status' => 'Voice Placement created. ', ]);
	}
	
	public function show($singerId): View
    {
		$singer = Singer::find($singerId);
	
		return view('singers.show', compact('singer'));
	}

    public function edit($singerId): View
    {
        $singer = Singer::find($singerId);

        return view('singers.edit', compact('singer' ));
    }
    public function update($singerId, Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'	=> 'required',
            'email' => [
                'required',
                Rule::unique('singers')->ignore($singerId),
            ],
        ]);

        $singer = Singer::find($singerId);

        $singer->name  = $request->name;
        $singer->email = $request->email;

        $singer->save();

        // Exit
        return redirect()->route('singers.show', [$singerId])->with(['status' => 'Singer saved. ']);
    }


    public function auditionpass($email): RedirectResponse
    {
		$Drip = new Drip( config('app.drip_token'), config('app.drip_account')); 
		
		// Add 'Passed Vocal Assessment' Tag
		$args = array(
			'tags' => array(
					array(
					'email' => $email,
					'tag' => 'Passed Vocal Assessment'
				),
			)
		);
		$Response = $Drip->post('tags', $args);
		
		if( isset($Reponse->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save audition results. ', 'Response' => $Response]);
		}
		
		// Add 'Completed Vocal Assessment' Event
		$args = array(
			'events' => array(
					array(
					'email' => $email,
					'action' => 'Completed Vocal Assessment'
				),
			)
		);
		$Response = $Drip->post('events', $args);
		
		if( isset($Reponse->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save audition results. ', 'Response' => $Response]);
		}
		
		return redirect('/singers')->with(['status' => 'The singer\'s audition result has been saved. ', 'Response' => $Response]);

	}
	
	public function feespaid($email): RedirectResponse
    {
		$Drip = new Drip( config('app.drip_token'), config('app.drip_account')); 
		
		// Add 'Membership Fees Paid' Tag
		$args = array(
			'tags' => array(
				array(
					'email' => $email,
					'tag' => 'Membership Fees Paid'
				),
				array(
					'email' => $email,
					'tag' => 'Waiting for Account Creation'
				),
			)
		);
		$Response = $Drip->post('tags', $args);
		
		if( isset($Reponse->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save fee status. ', 'Response' => $Response]);
		}
		
		$Response2 = $Drip->delete("subscribers/$email/tags/Prospective-Member");
		if( isset($Reponse2->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save fee status. ', 'Response' => $Response2]);
		}
		
		$Response3 = $Drip->delete("subscribers/$email/tags/Non-Member");
		if( isset($Reponse3->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save fee status. ', 'Response' => $Response3]);
		}
		
		return redirect('/singers')->with(['status' => 'The singer\'s fee status has been saved. ', 'Response' => $Response]);

	}
	
	public function markUniformProvided($email): RedirectResponse
    {
		$Drip = new Drip( config('app.drip_token'), config('app.drip_account')); 
		
		// Add 'Uniform Provided' Tag
		$args = array(
			'tags' => array(
				array(
					'email' => $email,
					'tag' => 'Uniform Provided'
				),
			)
		);
		$Response = $Drip->post('tags', $args);
		
		if( isset($Reponse->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save uniform status. ', 'Response' => $Response]);
		}
		
		return redirect('/singers')->with(['status' => 'The singer\'s uniform status has been saved. ', 'Response' => $Response]);

	}
	
	public function markAccountCreated($email): RedirectResponse
    {
		$Drip = new Drip( config('app.drip_token'), config('app.drip_account')); 
		
		// Add 'Account Created' Tag
		$args = array(
			'email' => $email,
			'tags' => array(
				'Account Created',
			),
			'remove_tags' => array(
				'Waiting for Account Creation',
			),
		);
		$Response = $Drip->post('subscribers', $args);
		
		if( isset($Reponse->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save account status. ', 'Response' => $Response]);
		}
		
		return redirect('/singers')->with(['status' => 'The singer\'s account status has been saved. ', 'Response' => $Response]);

	}

	public function move(Request $request, $singerId): RedirectResponse
    {
        $singer = Singer::find($singerId);

        $category = $request->input('move_category', 0);

        if( $category === 0 ) return redirect('/singers')->with([ 'status' => 'No category selected. ', 'fail' => true ]);

        // Attach to Prospects category
        $singer->category()->associate($category);
        $singer->save();

        return redirect('/singers')->with(['status' => 'The singer was moved. ']);
    }

    public function import(): RedirectResponse
    {

        // Default location: /storage/app
        Excel::import(new DripSingersImport(), 'subscribers.csv');

        // Exit
        return redirect('/singers')->with(['status' => 'Import done. ', ]);
    }
	
	public function export(): void
    {
		
		// Get subscribers
		$Drip = new Drip( config('app.drip_token'), config('app.drip_account')); 
		$args = array(
			'tags' => 'Member',
		);
		$Response = $Drip->get('subscribers', $args);
		$singers = $Response->subscribers; // Todo: add error handling.
		
		if( empty($singers) ) return;
		
		$rows = array();
		
		foreach ($singers as $singer) { 
		
			if( ! in_array( 'Waiting for Account Creation', $singer['tags'] ) ) 
				continue;
				
			// Process fields
			$name = ( isset($singer['custom_fields']['Name']) ? $singer['custom_fields']['Name'] : 'Unknown Unknown'  );
			$names = explode(' ', $name);
			$first_name = $names[0];
			$last_name = ( ! empty($names[1]) ) ? $names[1] : 'Unknown';
			
			$voice_part = ( isset($singer['custom_fields']['Voice_Part']) ? $singer['custom_fields']['Voice_Part'] : ''  );
			
			// Pack fields
			$cell = array( 
				'Login name'	=> $name,
				'Email'			=> $singer['email'],
				'Can Log In'	=> true,
				'Roles'			=> 'Member',
				'First name'	=> $first_name,
				'Last name' 	=> $last_name,
				'Nickname'  	=> '',
				'Street'		=> '',
				'Additional'	=> '',
				'City'			=> '',
				'Province'		=> '',
				'Postal code'	=> '',
				'Country'	 	=> '',
				'Mobile phone'	=> '',
				'Home phone'	=> '',
				'Work phone'	=> '',
				'Birthday'		=> '',
				'Notes'			=> '',
				'Voice part'	=> $voice_part,
				'Member ID'		=> '',
				'Skills'		=> '',
				'Member since'	=> '',
				'Dues paid until'	=> '',
				'Voice type'	=> '',
				'Height'		=> '',
				'Parent'		=> '',
				'Spouse name'	=> '',
				'Spouse Birthday'	=> '',
				'Anniversary'	=> '',
			);
			$rows[] = $cell;
		}
		
		// Make file
		Excel::create('Users', function($excel) use($rows) {
			
			$excel->sheet('Main', function($sheet) use($rows) {

				 $sheet->fromArray( $rows );

			});
			
		})->download('csv');
		
	}
}
