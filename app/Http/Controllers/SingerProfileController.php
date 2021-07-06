<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Singer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SingerProfileController extends Controller
{
	const PROFILE_TASK_ID = 1;

	public function __construct()
	{
		$this->authorizeResource(Singer::class, 'singer');
	}

	public function edit(Singer $singer, Request $request): View
	{
		return view('singers.editprofile', ['singer' => $singer, 'user' => $singer->user]);
	}

	public function update(ProfileRequest $request, Singer $singer): RedirectResponse
	{
	    $singer->update($request->only([
	        'referrer',
            'reason_for_joining',
            'membership_details',
        ]));
	    $singer->user->update($request->except([
            'referrer',
            'reason_for_joining',
            'membership_details',
        ]));

		return redirect()
			->route('singers.show', $singer)
			->with(['status' => 'Member Profile updated.']);
	}
}
