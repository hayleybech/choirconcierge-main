<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Libraries\Drip\Drip;
use Excel;

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
		
		$Response = self::getSingersByTag('Category - '. $category);
		if( isset($Reponse->error) ) {
			return view('singers', compact('Response'));
		}
		$singers = $Response->subscribers;
			
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
	
	public function show() {
		// find
	
		// return view('singers.show', compact('singer'));
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
