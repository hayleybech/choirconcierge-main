<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Imports\DripSingersImport;
use App\Libraries\Drip\Response;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\Singer;
use App\Models\Task;
use App\Models\SingerCategory;
use App\Libraries\Drip\Drip;
use Illuminate\Support\Str;
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
	
	public function store(): RedirectResponse
    {
        $PROSPECTS_CAT_ID = 1;
        $MEMBERS_CAT_ID = 3;

        $data = $this->validateRequest();
        $singer = Singer::create($data);

        if( $singer->onboarding_enabled ){
            // Attach all tasks
            $tasks = Task::all();
            $singer->tasks()->attach( $tasks );

            $category_id = $PROSPECTS_CAT_ID;
        } else {
            $category_id = $MEMBERS_CAT_ID;
        }

		// Attach to category
        $category = SingerCategory::find($category_id);
        $singer->category()->associate($category);

        // Add matching user
        $user = new User();
        $user->name = $singer->name;
        $user->email = $singer->email;
        if( isset($data['password']) && ! empty($data['password']) ) {
            $user->password = $data['password'];
        } else {
            $user->password = Str::random(10);
        }
        $user->save();

        // Sync roles
        $user_roles = $data['user_roles'] ?? [];
        $user->roles()->sync($user_roles);
        $user->save();

        $singer->user()->associate($user);

        $singer->save();
		
		// Exit
		return redirect('/singers')->with(['status' => 'Singer created. ', ]);
	}

    public function delete(Singer $singer): RedirectResponse
    {
        $singer->user->delete();
        $singer->delete();

        return redirect()->route('singers.index')->with(['status' => 'Singer deleted. ', ]);
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

	public function show(Singer $singer): View
    {
		return view('singers.show', compact('singer'));
	}

    public function edit(Singer $singer): View
    {
        return view('singers.edit', compact('singer' ));
    }
    public function update(Singer $singer, Request $request): RedirectResponse
    {
        // Update singer
        $data = $this->validateRequest($singer);
        $singer->update($data);

        // Update user
        $singer->user->email = $data['email'];
        $singer->user->name = $data['name'];
        if( isset($data['password']) && ! empty($data['password']) ) {
            $singer->user->password = $data['password'];
        }
        $singer->user->save();

        // Sync roles
        $user_roles = $data['user_roles'] ?? [];
        $singer->user->roles()->sync($user_roles);
        $singer->save();

        // Exit
        return redirect()->route('singers.show', [$singer])->with(['status' => 'Singer saved. ']);
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

	public function move(Singer $singer, Request $request): RedirectResponse
    {
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

    /**
     * @param Singer $singer
     * @return mixed
     */
    public function validateRequest(Singer $singer = null)
    {
        return request()->validate([
            'name'	=> 'required',
            'email'	=> [
                'required',
                Rule::unique('singers')->ignore($singer->id ?? ''),
            ],
            'onboarding_enabled'    => 'boolean',
            'user_roles' => [
                'array',
                'exists:roles,id',
            ],
            'password' => 'confirmed|nullable',
        ]);
    }
}
