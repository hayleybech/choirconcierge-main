<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Role;

class UsersController extends Controller
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
		
		//$users = User::all();
		$users = User::with('roles')->get();
		
		return view('users.index', compact('users'));
	}
	
	public function create(){
		$user = User::create(
		array(
			'name' => '',
			'email' => '',
		));
		$user->addRole('admin');
	}
	
	public function addRoles( $roles ) {
		
	}
}
