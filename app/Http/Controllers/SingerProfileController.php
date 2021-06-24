<?php

namespace App\Http\Controllers;

use App\Events\TaskCompleted;
use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use App\Models\Singer;
use App\Models\Task;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SingerProfileController extends Controller
{
	const PROFILE_TASK_ID = 1;

	public function __construct()
	{
		$this->authorizeResource(Profile::class, 'profile');
	}

	public function create(Singer $singer): View
	{
		return view('singers.createprofile', compact('singer'));
	}

	public function store(Singer $singer, ProfileRequest $request): RedirectResponse
	{
		$singer->profile()->create($request->validated()); // refer to whitelist in model

		if ($singer->onboarding_enabled) {
			// Mark matching task completed
			//$task = $singer->tasks()->where('name', 'Member Profile')->get();
			$singer->tasks()->updateExistingPivot(self::PROFILE_TASK_ID, ['completed' => true]);

			event(new TaskCompleted(Task::find(self::PROFILE_TASK_ID), $singer));
		}

		return redirect()
			->route('singers.show', $singer)
			->with(['status' => 'Member Profile created. ']);
	}

	public function edit(Singer $singer, Profile $profile, Request $request): View
	{
		return view('singers.editprofile', compact('singer', 'profile'));
	}

	public function update(ProfileRequest $request, Singer $singer, Profile $profile): RedirectResponse
	{
		$profile->update($request->validated());

		return redirect()
			->route('singers.show', $singer)
			->with(['status' => 'Member Profile updated.']);
	}
}
