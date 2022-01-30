<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Singer;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AccountController extends Controller
{
	public function edit(): View|Response
	{
        if(config('features.rebuild')) {
            return Inertia::render('Account/Edit');
        }

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
