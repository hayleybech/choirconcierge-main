<?php

namespace App\Http\Controllers;

use App\Models\Singer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashController extends Controller
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

    /**
     * Show the application dashboard.
     *
     * @return View
     */
    public function index(): View
    {
    	$birthdays = Singer::query()
		    ->birthdays()
		    ->with('profile')
		    ->get()
		    ->sort(static function(Singer $singer1, Singer $singer2): int {
		    	// Sort by birthday

			    if( $singer1->profile->birthday->equalTo($singer2->profile->birthday) ) {
			        return 0;
			    }
			    return $singer1->profile->birthday < $singer2->profile->birthday ? -1 : 1;
		    });

        return view('dash', [
        	'birthdays' => $birthdays,
	        'empty_dobs' => Singer::query()
		        ->emptyDobs()
		        ->count(),
        ]);
    }
}
