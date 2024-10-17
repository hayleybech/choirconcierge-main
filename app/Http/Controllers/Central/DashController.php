<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Membership;
use App\Models\Song;
use App\Models\Tenant;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashController extends Controller
{
	public function index(): Response
	{
		return Inertia::render('Central/Dash/Show', [
			'events' => $this->getEvents()->values(),
			'songs' => $this->getSongs()->values(),
			'tenantStats' => $this->getTenantStats(),
		]);
	}

	private function getEvents(): Collection
	{
		return Event::query()
			->whereIn('tenant_id', $this->getUserChoirs()->map(fn ($choir) => $choir->id))
			->whereBetween('call_time', [today(), today()->addMonth()])
			->orderBy('call_time')
			->with('tenant.domains')
			->get()
			->append(['my_rsvp']);
	}

	private function getSongs()
	{
		return Song::query()
			->whereIn('tenant_id', $this->getUserChoirs()->map(fn ($choir) => $choir->id))
			->whereHas('status', fn (Builder $query) => $query->where('title', 'Learning'))
			->orderBy('title')
			->with('tenant.domains')
			->get()
			->append('my_learning');
	}

	private function getUserChoirs()
	{
		return auth()->user()
			?->memberships()
			->withoutTenancy()
			->with('tenant.domains')
			->get()
			->map(fn($singer) => $singer->tenant);
	}

	private function getTenantStats()
	{
		if(! auth()->user()->isSuperAdmin()) {
			return null;
		};

		return [
			'activeTenants' => $this->getActiveTenantsCount(),
			'tenantsOnTrial' => $this->getTenantsOnTrialCount(),
			'tenantsTrialExpired' => $this->getTenantsTrialExpiredCount(),
			'activeMembers' => $this->getActiveMembersCount(),
		];
	}

	private function getActiveTenantsCount()
	{
		if(! auth()->user()->isSuperAdmin()) {
			return null;
		}

		return Tenant::active()->count();
	}

	private function getTenantsOnTrialCount()
	{
		if(! auth()->user()->isSuperAdmin()) {
			return null;
		}

		return Tenant::whereHas('subscriptions', function($query) {
			$query->onTrial();
		})->orWhereHas('customer', function($query) {
			// @todo make customer on trial scope?
			$query->whereNotNull('trial_ends_at')
				->where('trial_ends_at', '>', Carbon::now());
		})->count();
	}

	private function getTenantsTrialExpiredCount()
	{
		if(! auth()->user()->isSuperAdmin()) {
			return null;
		}

		return Tenant::whereHas('subscriptions', function($query) {
			$query->expiredTrial();
		})->orWhereHas('customer', function($query) {
			// @todo make customer expired trial scope?
			$query->whereNotNull('trial_ends_at')
				->where('trial_ends_at', '<', Carbon::now());
		})->count();
	}

	private function getActiveMembersCount()
	{
		if(! auth()->user()->isSuperAdmin()) {
			return null;
		}

		return Membership::whereHas('category', function($query) {
			$query->where('name', 'Members');
		})->whereHas('tenant', function($query) {
			$query->active();
		})->count();
	}
}
