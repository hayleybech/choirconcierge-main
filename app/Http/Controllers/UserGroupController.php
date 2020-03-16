<?php

namespace App\Http\Controllers;

use App\Models\UserGroup;
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

        UserGroup::create($data);

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
        return view('groups.edit', compact('group' ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UserGroup $group
     * @return RedirectResponse
     */
    public function update(UserGroup $group): RedirectResponse
    {
        $data = $this->validateRequest($group);

        $group->update($data);

        return redirect()->route('groups.show', [$group])->with(['status' => 'Group updated. ', ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\UserGroup  $userGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(UserGroup $userGroup)
    {
        //
    }

    /**
     * @param UserGroup $group
     * @return mixed
     */
    public function validateRequest(UserGroup $group)
    {
        return request()->validate([
            'title'             => 'required|max:255',
            'slug'              => [
                'required',
                Rule::unique('user_groups')->ignore($group->id),
                'max:255'
            ],
            'list_type'         => 'required',
        ]);
    }
}
