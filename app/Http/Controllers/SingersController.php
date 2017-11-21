<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Libraries\Drip\Drip;

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
		$Drip = new Drip('nsef8o9sjpmfake3czoq', '9922956');
	
		// Get subscribers
		$Response = $Drip->get('subscribers');
		$singers = $Response->subscribers; 
		
		return view('singers', compact('singers', 'Response'));
	}
	
	public function show() {
		// find
	
		// return view('singers.show', compact('singer'));
	}
	
	public function auditionpass($email) {
		$Drip = new Drip('nsef8o9sjpmfake3czoq', '9922956');
		
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
					'email' => $_GET['action_email'],
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
		$Drip = new Drip('nsef8o9sjpmfake3czoq', '9922956');
		
		// Add 'Membership Fees Paid' Tag
		$args = array(
			'tags' => array(
					array(
					'email' => $email,
					'tag' => 'Membership Fees Paid'
				),
			)
		);
		$Response = $Drip->post('tags', $args);
		
		if( isset($Reponse->error) ) {
			return redirect('/singers')->with(['status' => 'Could not save fee status. ', 'Response' => $Response]);
		}
		
		return redirect('/singers')->with(['status' => 'The singer\'s fee status has been saved. ', 'Response' => $Response]);

	}
	
	
	// https://stackoverflow.com/questions/26146719/use-laravel-to-download-table-as-csv/27596496#27596496
	public function export() {
		$headers = [
				'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0'
			,   'Content-type'        => 'text/csv'
			,   'Content-Disposition' => 'attachment; filename=galleries.csv'
			,   'Expires'             => '0'
			,   'Pragma'              => 'public'
		];

		//$list = Singer::all()->toArray();
		
		// Get subscribers
		$Drip = new Drip('nsef8o9sjpmfake3czoq', '9922956');
		$Response = $Drip->get('subscribers');
		$singers = $Response->subscribers; 
		

		# add headers for each column in the CSV download
		//array_unshift($list, array_keys($list[0]));

	   //$callback = function() use ($list) 
	   $callback = function() use ($singers) 
		{
			$FH = fopen('php://output', 'w');
			foreach ($singers as $singer) { 
				
				$names = explode(' ', $singer['custom_fields']['name']);
				
				//fputcsv($FH, $row);
				fputcsv($FH, array( 
					'Login name'	=> singer['custom_fields']['name'],
					'Email'			=> singer['email'],
					'Can Log In'	=> true,
					'Roles'			=> 'Member',
					'First Name'	=> $names[0],
					//'Last Name' 	=> $names[1],
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
					'Voice Part'	=> singer['custom_fields']['Voice Part'],
					'Member ID'		=> '',
					'Skills'		=> '',
					'Member Since'	=> '',
					'Dues paid until'	=> '',
					'Voice type'	=> '',
					'Height'		=> '',
					'Parent'		=> '',
					'Spouse name'	=> '',
					'Spouse birthday'	=> '',
					'Anniversary'	=> '',
				) );
			}
			fclose($FH);
		};

		return response()->stream($callback, 200, $headers);
	}
}
