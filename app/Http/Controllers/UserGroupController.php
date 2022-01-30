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
use Inertia\Inertia;
use Inertia\Response;

class UserGroupController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(UserGroup::class, 'group');
	}
	/**
	 * Display a listing of the resource.
	 */
	public function index(): View|Response
	{
		$groups = UserGroup::with('tenant')->orderBy('title')->get();
		$filters = [];

        if(config('features.rebuild')){
            return Inertia::render('MailingLists/Index', [
                'lists' => $groups->values(),
            ]);
        }

		return view('groups.index', compact('groups', 'filters'));
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View|Response
	{
		$voice_parts = VoicePart::all();
		$singer_categories = SingerCategory::all();

        if(config('features.rebuild')){
            return Inertia::render('MailingLists/Create', [
                'roles' => Role::where('name', '!=', 'User')->get()->values(),
                'voiceParts' => $voice_parts->values(),
                'singerCategories' => $singer_categories->values(),
            ]);
        }

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
	public function show(UserGroup $group): View|Response
	{
	    $group->load('members.memberable', 'senders.sender');

        $group->can = [
            'update_group' => auth()->user()?->can('update', $group),
            'delete_group' => auth()->user()?->can('delete', $group),
        ];

        if(config('features.rebuild')){
            return Inertia::render('MailingLists/Show', [
                'list' => $group,
            ]);
        }

		return view('groups.show', compact('group'));
	}

	public function edit(UserGroup $group): View|Response
	{
        $group->load([
            'recipient_roles', 'recipient_voice_parts', 'recipient_singer_categories', 'recipient_users',
            'sender_roles', 'sender_voice_parts', 'sender_singer_categories', 'sender_users',
        ]);

		$voice_parts = VoicePart::all();
		$singer_categories = SingerCategory::all();

        if(config('features.rebuild')){
            return Inertia::render('MailingLists/Edit', [
                'list' => $group,
                'roles' => Role::where('name', '!=', 'User')->get()->values(),
                'voiceParts' => $voice_parts->values(),
                'singerCategories' => $singer_categories->values(),
            ]);
        }

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
