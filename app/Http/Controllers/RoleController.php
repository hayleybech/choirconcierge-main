<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(Role::class, 'role');
	}

	public function index(): View|Response
	{
		$roles = Role::withCount('singers')->get();

        if(config('features.rebuild')){
            Inertia::setRootView('layouts/app-rebuild');

            return Inertia::render('Roles/Index', [
                'roles' => $roles->values(),
            ]);
        }

		return view('roles.index', [
			'roles' => $roles,
		]);
	}

	public function create(): View
	{
		return view('roles.create');
	}

	public function store(Request $request): RedirectResponse
	{
		$data = $request->validate([
			'name' => 'required|max:255',
			'abilities' => 'array',
		]);
		$role = Role::create($data);
		return redirect()
			->route('roles.show', $role)
			->with(['status' => 'Role created.']);
	}

	public function show(Role $role): View
	{
		return view('roles.show', [
			'role' => $role,
		]);
	}

	public function edit(Role $role): View
	{
		return view('roles.edit')->with(['role' => $role]);
	}

	public function update(Request $request, Role $role): RedirectResponse
	{
		$data = $request->validate([
			'name' => 'required|max:255',
			'abilities' => 'array',
		]);
		$role->update($data);

		return redirect()
			->route('roles.show', $role)
			->with(['status' => 'Role saved.']);
	}

	public function destroy(Role $role): RedirectResponse
	{
		$role->delete();
		return redirect()
			->route('roles.index')
			->with(['status' => 'Role deleted.']);
	}
}
