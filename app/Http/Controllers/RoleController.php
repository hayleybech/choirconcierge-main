<?php

namespace App\Http\Controllers;

use App\Models\Role;;
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

    public function index(): Response
    {
        return Inertia::render('Roles/Index', ['roles' => Role::withCount('singers')->get()->values()]);
    }

    public function create(): Response
    {
        return Inertia::render('Roles/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $role = Role::create($request->validate([
            'name' => 'required|max:255',
            'abilities' => 'array',
        ]));

        return redirect()
            ->route('roles.show', $role)
            ->with(['status' => 'Role created.']);
    }

    public function show(Role $role): Response
    {
        $role->can = [
            'update_role' => auth()->user()?->can('update', $role),
            'delete_role' => auth()->user()?->can('delete', $role),
        ];

        return Inertia::render('Roles/Show', ['role' => $role]);
    }

    public function edit(Role $role): Response
    {
        return Inertia::render('Roles/Edit', ['role' => $role]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $role->update($request->validate([
            'name' => 'required|max:255',
            'abilities' => 'array',
        ]));

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
