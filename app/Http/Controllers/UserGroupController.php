<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserGroupRequest;
use App\Models\Folder;
use App\Models\GroupMember;
use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\User;
use App\Models\UserGroup;
use App\Models\VoicePart;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserGroupController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(UserGroup::class, 'group');
	}
	/**
	 * Display a listing of the resource.
	 */
	public function index(): View
	{
		$groups = UserGroup::all()->sortBy('title');
		$filters = [];
		return view('groups.index', compact('groups', 'filters'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View
	{
		$voice_parts = VoicePart::all();
		$singer_categories = SingerCategory::all();
		return view('groups.create', compact('voice_parts', 'singer_categories'));
	}

	/**
	 * Store a newly created resource in storage.
	 * @param UserGroupRequest $request
	 * @return RedirectResponse
	 */
	public function store(UserGroupRequest $request): RedirectResponse
	{
		$group = UserGroup::create($request->validated());

		return redirect()
			->route('groups.show', [$group])
			->with(['status' => 'Group created. ']);
	}

	/**
	 * Display the specified resource.
	 * @param UserGroup $group
	 * @return View
	 */
	public function show(UserGroup $group): View
	{
		return view('groups.show', compact('group'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  UserGroup  $group
	 * @return View
	 */
	public function edit(UserGroup $group): View
	{
		$voice_parts = VoicePart::all();
		$singer_categories = SingerCategory::all();
		return view('groups.edit', compact('group', 'voice_parts', 'singer_categories'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param UserGroup $group
	 * @param UserGroupRequest $request
	 *
	 * @return RedirectResponse
	 */
	public function update(UserGroup $group, UserGroupRequest $request): RedirectResponse
	{
		$group->update($request->validated());

		return redirect()
			->route('groups.show', [$group])
			->with(['status' => 'Group updated. ']);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param UserGroup $group
	 * @return RedirectResponse
	 */
	public function destroy(UserGroup $group): RedirectResponse
	{
		try {
			$group->delete();
		} catch (\Exception $e) {
			return redirect()
				->route('groups.index')
				->with(['status' => 'Group could not be deleted. Check that the group ID exists. ']);
		}

		return redirect()
			->route('groups.index')
			->with(['status' => 'Group deleted. ']);
	}
}
