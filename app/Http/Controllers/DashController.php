<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Membership;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Inertia\Inertia;
use Inertia\Response;

class DashController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): Response
    {
        return Inertia::render('Dash/Show', [
            'events' => $this->getEvents()->values(),
            'songs' => $this->getSongs()->values(),
            'birthdays' => $this->getBirthdays()->values(),
            'emptyDobs' => $this->getEmptyDobs(),
            'memberversaries' => $this->getMemberversaries()->values(),
	        'feeStatus' => auth()->user()->membership?->fee_status,
        ]);
    }

    private function getMemberversaries()
    {
        return Membership::query()
            ->with('user')
            ->active()
            ->memberversaries()
            ->get()
            ->append('memberversary')
            ->sortBy('joined_at');
    }

    private function getEvents(): Collection
    {
        return Event::query()
            ->whereBetween('call_time', [today(), today()->addMonth()])
            ->orderBy('call_time')
            ->get()
            ->append(['my_rsvp']);
    }

    private function getSongs()
    {
        return Song::whereHas('status', fn (Builder $query) => $query->where('title', 'Learning'))
            ->orderBy('title')
            ->get()
            ->append('my_learning');
    }

    private function getEmptyDobs()
    {
        return Membership::query()
            ->with('user')
            ->emptyDobs()
            ->count();
    }

    private function getBirthdays()
    {
        return User::query()
            ->whereHas('memberships', fn($query) => $query->active())
            ->birthdays()
            ->get()
            ->append('birthday')
            ->sortBy('birthday');
    }
}
