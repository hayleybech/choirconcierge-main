<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\User;
use App\Models\Role;
use Illuminate\View\View;

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
	
	public function index(): View
    {
		
		//$users = User::all();
		$users = User::with('roles')->get();
		$roles_all = Role::all();
		
		return view('users.index', compact('users', 'roles_all'));
	}
	
	public function create(): void
    {
		$user = User::create(
		array(
			'name' => '',
			'email' => '',
		));
		$user->addRole('admin');
		// @todo add missing redirect??
	}
	
	public function addRoles( Request $request, $userid ): RedirectResponse
    {
		$user = \App\Models\User::find($userid);
		$roles = $request->input('roles');
		
		$user->addRoles($roles);
		
		return redirect('/users');
	}
	
	public function detachRole($userid, $role): RedirectResponse
    {
		$user = \App\Models\User::find($userid);
		$user->detachRole($role);
		return redirect('/users');
	}
}
