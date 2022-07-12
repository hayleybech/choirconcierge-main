<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserGroupRequest;
use App\Models\Role;
use App\Models\SingerCategory;
use App\Models\UserGroup;
use App\Models\VoicePart;
use Illuminate\Http\RedirectResponse;
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
    public function index(): Response
    {
        return Inertia::render('MailingLists/Index', [
            'lists' => UserGroup::with('tenant')->orderBy('title')->get()->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('MailingLists/Create', [
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
            'voiceParts' => VoicePart::all()->values(),
            'singerCategories' => SingerCategory::all()->values(),
        ]);
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

    public function show(UserGroup $group): Response
    {
        $group->load('members.memberable', 'senders.sender');

        $group->can = [
            'update_group' => auth()->user()?->can('update', $group),
            'delete_group' => auth()->user()?->can('delete', $group),
        ];

        return Inertia::render('MailingLists/Show', [
            'list' => $group,
        ]);
    }

    public function edit(UserGroup $group): Response
    {
        $group->load([
            'recipient_roles', 'recipient_voice_parts', 'recipient_singer_categories', 'recipient_users',
            'sender_roles', 'sender_voice_parts', 'sender_singer_categories', 'sender_users',
        ]);

        return Inertia::render('MailingLists/Edit', [
            'list' => $group,
            'roles' => Role::where('name', '!=', 'User')->get()->values(),
            'voiceParts' => VoicePart::all()->values(),
            'singerCategories' => SingerCategory::all()->values(),
        ]);
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
