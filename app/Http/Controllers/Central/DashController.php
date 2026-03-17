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
		if(! auth()->user()->isSuperAdmin) {
			return null;
		};

		return [
			'activeTenants' => $this->getActiveTenantsCount(),
			'tenantsOnTrial' => $this->getTenantsOnTrialCount(),
			'tenantsTrialExpired' => $this->getTenantsTrialExpiredCount(),
			'activeMembers' => $this->getActiveMembersCount(),
			'trialConversionRate' => $this->getTrialConversionRate(),
			'medianRetentionTime' => $this->getMedianRetentionTime(),
		];
	}

	private function getTrialConversionRate()
	{
		if(! auth()->user()->isSuperAdmin) {
			return null;
		}

		$totalTrialed = Tenant::whereHas('customer', function($query) {
			$query->whereNotNull('trial_ends_at');
		})->orWhereHas('subscriptions', function($query) {
			$query->whereNotNull('trial_ends_at');
		})->count();

		if ($totalTrialed === 0) {
			return 0;
		}

		$converted = Tenant::whereHas('subscriptions', function($query) {
			$query->where(function($q) {
				$q->whereNull('trial_ends_at')
					->orWhere('trial_ends_at', '<', Carbon::now());
			})->where('paddle_status', 'active');
		})->count();

		return round(($converted / $totalTrialed) * 100, 2);
	}

	private function getMedianRetentionTime()
	{
		if(! auth()->user()->isSuperAdmin) {
			return null;
		}

		$durations = Tenant::whereHas('subscriptions', function($query) {
			$query->where('paddle_status', 'active')
				->orWhereNotNull('ends_at');
		})->with('subscriptions')->get()->map(function($tenant) {
			$firstSub = $tenant->subscriptions->sortBy('created_at')->first();
			$lastSub = $tenant->subscriptions->sortByDesc('ends_at')->first();

			$start = Carbon::parse($firstSub->created_at);
			$end = $lastSub->ends_at ? Carbon::parse($lastSub->ends_at) : Carbon::now();

			return $start->diffInMonths($end);
		})->sort()->values();

		$count = $durations->count();

		if ($count === 0) {
			return 0;
		}

		$middle = floor(($count - 1) / 2);

		if ($count % 2) {
			return $durations->get($middle);
		}

		return round(($durations->get($middle) + $durations->get($middle + 1)) / 2, 2);
	}

	private function getActiveTenantsCount()
	{
		if(! auth()->user()->isSuperAdmin) {
			return null;
		}

		return Tenant::active()->count();
	}

	private function getTenantsOnTrialCount()
	{
		if(! auth()->user()->isSuperAdmin) {
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
		if(! auth()->user()->isSuperAdmin) {
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
		if(! auth()->user()->isSuperAdmin) {
			return null;
		}

		return Membership::whereHas('category', function($query) {
			$query->where('name', 'Members');
		})->whereHas('tenant', function($query) {
			$query->active();
		})->count();
	}
}
