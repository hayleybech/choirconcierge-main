<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Singer;
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
	        'feeStatus' => auth()->user()->singer?->fee_status,
        ]);
    }

    private function getMemberversaries()
    {
        return Singer::query()
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
        return Singer::query()
            ->with('user')
            ->emptyDobs()
            ->count();
    }

    private function getBirthdays()
    {
        return User::query()
            ->whereHas('singers', fn($query) => $query->active())
            ->birthdays()
            ->get()
            ->append('birthday')
            ->sortBy('birthday');
    }
}
