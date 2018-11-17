<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Imports\DripSingersImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Singer;
use App\Task;
use App\Libraries\Drip\Drip;
use Maatwebsite\Excel\Facades\Excel;

class SingersController extends Controller
{
	
	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

	
    public function index(){
		
		$category = Input::get('filter_category', 'Prospective Member');
		
		/*
		$Response = self::getSingersByTag('Category - '. $category);
		if( isset($Reponse->error) ) {
			return view('singers', compact('Response'));
		}
		$singers = $Response->subscribers;
		*/
		
		// Replace Drip code with DB code
		//$singers = Singer::all();
		$singers = Singer::with('tasks')->get();
			
		/*$membersResponse = self::getMembersPaid();
		if( isset($membersReponse->error) ) {
			return view('singers', compact('Response'));
		}
		$members = $membersResponse->Subscribers;*/
		
		return view('singers', compact('category', 'singers', 'members', 'Response'));
	}
	
		public function getSingersByTag($tag) {
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
	
		public function getProspects() {
			return self::getSingersByTag('Prospective Member');
		}
		
		public function getMembersPaid() {
			return self::getSingersByTag('Waiting for Account Creation');
		}
	
	public function create() {
		return view('singer.create');
	}
	
	public function store(Request $request) {
		$validated = $request->validate([
			'name'	=> 'required',
			'email'	=> 'required',
		]);
		
		$singer = new Singer();
		
		$singer->name  = $request->name;
		$singer->email = $request->email;
		
		$singer->save();
		
		// Attach all tasks

		$tasks = Task::all();
		$singer->tasks()->attach( $tasks );
		
		// Exit
		return redirect('/singers')->with(['status' => 'Singer created. ', ]);
	}
	
	public function completeTask($singerId, $taskId) {
		$singer = Singer::find($singerId);
		$task = Task::find($taskId);

        event(new TaskCompleted($task, $singer));

		// Complete type-specific action
		if( $task->type == 'manual' ) {
			// Simply mark as done. 
			$singer->tasks()->updateExistingPivot($taskId, ['completed' => true]);
			return redirect('/singers')->with(['status' => 'Task updated. ', ]);
		} else {
			// Redirect to form
			
		}
	}
	
	public function createProfile($singerId) {
		$singer = Singer::find($singerId);
		
		return view('singer.createprofile', compact('singer'));
	}
	
	public function storeProfile(Request $request) {
		
		$singer = Singer::find($request->singer_id);
		$singer->profile()->create($request->all()); // refer to whitelist in model
		
		// Mark matching task completed
		//$task = $singer->tasks()->where('name', 'Member Profile')->get();
		$singer->tasks()->updateExistingPivot(1, array('completed' => true) );
		
		return redirect('/singers')->with(['status' => 'Member Profile created. ', ]);
	}
	
	public function createPlacement($singerId) {
		$singer = Singer::find($singerId);
		
		return view('singer.createplacement', compact('singer'));
	}
	
	public function storePlacement(Request $request) {
		$singer = Singer::find($request->singer_id);
		$singer->placement()->create($request->all()); // refer to whitelist in model
		
		// Mark matching task completed
		//$task = $singer->tasks()->where('name', 'Voice Placement')->get();
		$singer->tasks()->updateExistingPivot(2, array('completed' => true) );
		
		return redirect('/singers')->with(['status' => 'Voice Placement created. ', ]);
	}
	
	public function show($singerId) {
		$singer = Singer::find($singerId);
	
		return view('singer.show', compact('singer'));
	}
	
	public function auditionpass($email) {
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
	
	public function feespaid($email) {
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
	
	public function markUniformProvided($email) {
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
	
	public function markAccountCreated($email) {
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
	
	public function moveToArchive($email) {
		$Drip = new Drip( config('app.drip_token'), config('app.drip_account')); 
		
		// Add 'Account Created' Tag
		$args = array(
			'subscribers' => array(
				[
					'email' => $email,
					'tags' => array(
						'Category - Archived',
					),
					'remove_tags' => array(
						'Category - Prospective Member',
					),
				]
			)
		);
		$Response = $Drip->post('subscribers', $args);
		
		if( isset($Reponse->error) ) {
			return redirect('/singers')->with(['status' => 'Could not move singer. ', 'Response' => $Response]);
		}
		return redirect('/singers')->with(['status' => 'The singer was moved. ', 'Response' => $Response]);
	}

    public function import() {

        // Default location: /storage/app
        Excel::import(new DripSingersImport(), 'subscribers.csv');

        // Exit
        return redirect('/singers')->with(['status' => 'Import done. ', ]);
    }
	
	public function export() {
		
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
