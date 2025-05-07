<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Khsing\World\Models\Continent;
use Khsing\World\Models\Country;

class AccountController extends Controller
{
    public function edit(): View|Response
    {
        return Inertia::render('Account/Edit', [
			'countriesByContinent' => Continent::select(['id', 'name'])->with(['countries:id,continent_id,name'])->get(),
        ]);
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        auth()->user()->update($request->validated());

        return redirect()
            ->route('singers.show', auth()->user()->membership)
            ->with(['status' => 'Account Settings updated.']);
    }
}
