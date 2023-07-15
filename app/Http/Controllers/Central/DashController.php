<?php

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Song;
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
}
