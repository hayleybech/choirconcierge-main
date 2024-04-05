<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use DateTimeZone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\Response as InertiaResponse;

class TenantController extends Controller
{
	// @todo add missing auth

    public function index(): Response
    {
        return Inertia::render('Central/Tenants/Index', [
            'tenants' => Tenant::with('domains')->get()->append(['billing_status'])->values(),
        ]);
    }

	public function create(): InertiaResponse
	{
		return Inertia::render('Central/Tenants/Create', [
			'centralDomain' => central_domain(),
            'timezones' => DateTimeZone::listIdentifiers(),
		]);
	}

	public function store(Request $request): RedirectResponse
	{
		$request->validate([
			'name' => ['required', 'max:127'],
			'logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png,image/jpeg', 'max:10240'],
			'primary_domain' => ['required', 'max:127'],
			'timezone' => ['required', Rule::in(DateTimeZone::listIdentifiers())],

			'ensemble_name' => ['required', 'max:127'],
			'ensemble_logo' => ['sometimes', 'nullable', 'file', 'mimetypes:image/png,image/jpeg', 'max:10240'],
		]);

		// Create tenant
		$tenant = Tenant::create(
			$request->input('primary_domain'),
			$request->input('name'),
			$request->input('timezone'),
			[
				'created_by' => auth()->user()->id,
			]
		);
		if($request->hasFile('logo')) {
			$tenant->updateLogo($request->file('logo'), $request->file('logo')->hashName());
		}

		// Create domain
		$tenant->domains()->create([
			'domain' => $request->input('primary_domain'),
			'is_primary' => true,
		]);

		// Create ensemble
		$ensemble = $tenant->ensembles()->create([
			'name' => $request->input('ensemble_name'),
		]);
		if($request->hasFile('ensemble_logo')) {
			$ensemble->updateLogo($request->file('ensemble_logo'), $request->file('ensemble_logo')->hashName());
		}

		return redirect()->route('central.tenants.onboarding', ['tenant' => $tenant])->with(['status' => 'Organisation created.']);
	}

    public function show(Tenant $tenant): Response
    {
        return Inertia::render('Central/Tenants/Show', [
            'tenant' => $tenant->append(['billing_status', 'plan', 'setup_done']),
        ]);
    }
}
