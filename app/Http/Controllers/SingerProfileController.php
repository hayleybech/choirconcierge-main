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
	    $singer->user->update($request->only([
            'dob',
            'phone',
            'ice_name',
            'ice_phone',
            'address_street_1',
            'address_street_2',
            'address_suburb',
            'address_state',
            'address_postcode',
            'profession',
            'skills',
            'height',
            'bha_id',
        ]));

		return redirect()
			->route('singers.show', $singer)
			->with(['status' => 'Member Profile updated.']);
	}
}
