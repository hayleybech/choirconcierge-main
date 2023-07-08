<?php

namespace App\Http\Controllers;

use App\Http\Requests\EnsembleRequest;
use App\Models\Ensemble;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;

class EnsembleController extends Controller
{
	public function __construct()
	{
		$this->authorizeResource(Ensemble::class);
	}

    public function store(EnsembleRequest $request, Tenant $organisation): RedirectResponse
    {
	    $ensemble = $organisation->ensembles()->create($request->safe()->only(['name']));

	    if($request->hasFile('logo')) {
		    $ensemble->updateLogo($request->file('logo'), $request->file('logo')->hashName());
	    }

	    return redirect()->back()->with(['status' => 'Ensemble created.']);
    }

    public function update(EnsembleRequest $request, Tenant $organisation, Ensemble $ensemble): RedirectResponse
    {
        $ensemble->update($request->safe()->only(['name']));

	    if($request->hasFile('logo')) {
		    $ensemble->updateLogo($request->file('logo'), $request->file('logo')->hashName());
	    }

	    return redirect()->back()->with(['status' => 'Ensemble saved.']);
    }
}
