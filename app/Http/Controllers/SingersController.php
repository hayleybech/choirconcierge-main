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
}
