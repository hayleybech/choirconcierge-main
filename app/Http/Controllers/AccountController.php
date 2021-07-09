<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Singer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
	public function edit(): View
	{
		return view('accounts.edit', ['user' => auth()->user()]);
	}

	public function update(ProfileRequest $request): RedirectResponse
	{
	    auth()->user()->update($request->validated());

		return redirect()
			->route('accounts.edit')
			->with(['status' => 'Account Settings updated.']);
	}
}
