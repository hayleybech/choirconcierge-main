<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateTenantRequest;
use App\Models\Tenant;
use App\Models\User;
use DateTimeZone;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Inertia\Response as InertiaResponse;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Stancl\Tenancy\Database\Models\Domain;

class TenantController extends Controller
{
    public function index(): Response
    {
        $this->authorize('viewAny', Tenant::class);

        $pagination = $this->getTenants();

        return Inertia::render('Central/Tenants/Index', [
            'tenants' => $pagination
                ->getCollection()
	            ->append(['billing_status']),
            'pagination' => $pagination,
        ]);
    }

	public function create(): InertiaResponse
	{

		return Inertia::render('Central/Tenants/Create', [
			'centralDomain' => central_domain(),
            'timezones' => DateTimeZone::listIdentifiers(),
		]);
	}

	public function store(CreateTenantRequest $request): RedirectResponse
	{
        // Create tenant
		$tenant = Tenant::create(
			$request->validated('primary_domain'),
			$request->validated('name'),
			$request->validated('timezone'),
			[
				'created_by' => auth()->user()->id,
			]
		);
		if($request->hasFile('logo')) {
			$tenant->updateLogo($request->file('logo'), $request->file('logo')->hashName());
		}

		// Create domain
		$tenant->domains()->create([
			'domain' => $request->validated('primary_domain'),
			'is_primary' => true,
		]);

		// Create ensemble
		$ensemble = $tenant->ensembles()->create([
			'name' => $request->validated('ensemble_name'),
		]);
		if($request->hasFile('ensemble_logo')) {
			$ensemble->updateLogo($request->file('ensemble_logo'), $request->file('ensemble_logo')->hashName());
		}

		return redirect()
            ->route('central.tenants.onboarding', ['tenant' => $tenant])
            ->with(['status' => 'Organisation created.']);
	}

    public function show(Tenant $tenant): Response
    {
        Gate::allowIf(fn (User $user) => $user->id === $tenant->created_by || $user->isSuperAdmin);

        return Inertia::render('Central/Tenants/Show', [
            'tenant' => $tenant->append(['billing_status', 'plan', 'setup_done', 'disk_usage']),
        ]);
    }

	private function getTenants() {
		return QueryBuilder::for(Tenant::class)
			->allowedFilters([
				'id',
                AllowedFilter::callback('billing_status', fn(Builder $query, $value) => match ($value) {
                    'active' => $query->active(),
                    'subscribed' => $query->subscribed(),
                    'inactive' => $query->inactive(),
                    'gratis' => $query->gratis(),
                    'trial' => $query->trial(),
                    default => $query,
                })
			])
			->defaultSort('id')
			->allowedSorts([
				'id',
				'created_at',
			])->with('domains')
            ->paginate(50)->appends(request()->query());
	}
}
