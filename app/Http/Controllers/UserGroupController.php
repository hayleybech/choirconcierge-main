<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $groups = UserGroup::all()->sortBy('title');
        $filters = [];
        return view('groups.index', compact('groups', 'filters') );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() : View
    {
        return view('groups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(): RedirectResponse
    {
        $data = $this->validateRequest();
        $group = UserGroup::create($data);

        // Update recipients
        // @todo refactor member attachment to separate method
        $members = [];
        foreach($data['recipient_roles'] as $role){
            $members[] = [
                'memberable_id'     => $role,
                'memberable_type'   => 'App\Models\Role',
            ];
        }
        foreach($data['recipient_users'] as $user){
            $members[] = [
                'memberable_id'     => $user,
                'memberable_type'   => 'App\Models\User',
            ];
        }
        $group->members()->createMany($members);

        return redirect('/groups')->with(['status' => 'Group created. ', ]);
    }

    /**
     * Display the specified resource.
     * @param UserGroup $group
     * @return View
     */
    public function show(UserGroup $group): View
    {
        return view('groups.show', compact('group' ));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  UserGroup  $group
     * @return View
     */
    public function edit(UserGroup $group): View
    {
        $roles = $group->members()->where('memberable_type', '=', Role::class)->get();
        $users = $group->members()->where('memberable_type', '=', User::class)->get();

        return view('groups.edit', compact('group', 'roles', 'users' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserGroup $group
     * @return RedirectResponse
     */
    public function update(UserGroup $group): RedirectResponse
    {
        // Update the group
        $data = $this->validateRequest($group);
        $group->update($data);

        // Update recipients
        // @todo refactor member attachment to separate method
        $members = [];
        foreach($data['recipient_roles'] as $role){
            $members[] = [
                'memberable_id'     => $role,
                'memberable_type'   => 'App\Models\Role',
            ];
        }
        foreach($data['recipient_users'] as $user){
            $members[] = [
                'memberable_id'     => $user,
                'memberable_type'   => 'App\Models\User',
            ];
        }
        $group->members()->createMany($members);

        return redirect()->route('groups.show', [$group])->with(['status' => 'Group updated. ', ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param UserGroup $group
     * @return RedirectResponse
     */
    public function destroy(UserGroup $group): RedirectResponse
    {
        try{
            $group->delete();
        } catch(\Exception $e) {
            return redirect()->route('groups.index')->with(['status' => 'Group could not be deleted. Check that the group ID exists. ', ]);
        }

        return redirect()->route('groups.index')->with(['status' => 'Group deleted. ', ]);
    }

    public function findRecipient(Request $request): JsonResponse
    {
        $term = trim($request->q);
        $type = trim($request->type);

        if( empty($term) ){
            return \Response::json([]);
        }

        $formatted_recipients = [];
        if( $type === 'users'){
            $users = User::where('name', 'like', "%$term%")
                ->limit(5)
                ->get();

            foreach( $users as $user ){
                $formatted_recipients[] = [
                    'id'    => $user->id,
                    'text'  => $user->name
                ];
            }
        } elseif( $type === 'roles' ){
            $roles = Role::where('name', 'like', "%$term%")
                ->limit(5)
                ->get();

            foreach( $roles as $role ){
                $formatted_recipients[] = [
                    'id'    => $role->id,
                    'text'  => $role->name
                ];
            }
        }

        return \Response::json($formatted_recipients);
    }

    /**
     * @param UserGroup $group
     * @return mixed
     */
    public function validateRequest(UserGroup $group = null)
    {
        return request()->validate([
            'title'             => 'required|max:255',
            'slug'              => [
                'required',
                Rule::unique('user_groups')->ignore($group->id ?? ''),
                'max:255'
            ],
            'list_type'         => 'required',
            'recipient_roles'  => '',
            'recipient_users'  => '',
        ]);
    }
}
