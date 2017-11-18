<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
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
		$roles_all = Role::all();
		
		return view('users.index', compact('users', 'roles_all'));
	}
	
	public function create(){
		$user = User::create(
		array(
			'name' => '',
			'email' => '',
		));
		$user->addRole('admin');
	}
	
	public function addRoles( $userid ) {
		$user = \App\User::find($userid);
		$roles = Input::get('roles');
		
		$user->addRoles($roles);
		
		return redirect('/users');
	}
	
	public function detachRole($userid, $role) {
		$user = \App\User::find($userid);
		$user->detachRole($role);
		return redirect('/users');
	}
}
