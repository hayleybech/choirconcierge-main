<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
use App\Models\Poll;
use App\Models\Song;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
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
            'eventCategories' => $this->getEventCategories(),
            'songs' => $this->getSongs()->values(),
            'birthdays' => $this->getBirthdays()->values(),
            'emptyDobs' => $this->getEmptyDobs(),
            'memberversaries' => $this->getMemberversaries()->values(),
            'feeStatus' => auth()->user()->membership?->fee_status,
            'attendanceSummary' => auth()?->user()?->membership ? $this->getAttendanceSummary(auth()?->user()?->membership) : null,
            'rsvpSummary' => auth()?->user()?->membership ? $this->getRsvpSummary(auth()?->user()?->membership) : null,
            'performanceTypeId' => EventType::where('title', 'Performance')->first()?->id,
            'activePolls' => $this->getActivePolls(),
        ]);
    }

    private function getMemberversaries()
    {
        return Membership::query()
            ->ensembleRestricted()
            ->with('user')
            ->select('*')
            ->active()
            ->memberversaries()
            ->havingBetween('upcoming_memberversary', [
                DB::raw('CURDATE()'),
                DB::raw('DATE_ADD(CURDATE(), INTERVAL 30 DAY)')
            ])
            ->get()
            ->append('memberversary')
            ->sortBy('joined_at');
    }

    private function getEvents(): Collection
    {
        return Event::query()
            ->whereBetween('call_time', [today(), today()->addMonth()])
            ->whereIn('type_id', tenant('widgets_upcoming_events_categories') ?? EventType::all()->pluck('id'))
            ->orderBy('call_time')
            ->get()
            ->append(['my_rsvp']);
    }

    private function getEventCategories(): \Illuminate\Support\Collection
    {
        if (!auth()->user()->can('create', Event::class)) {
            return collect();
        }
        return EventType::query()
            ->select(['id', 'title'])
            ->get()
            ->values();
    }

    private function getSongs()
    {
        return Song::whereHas('status', fn(Builder $query) => $query->where('title', 'Learning'))
            ->orderBy('title')
            ->get()
            ->append('my_learning');
    }

    private function getEmptyDobs()
    {
        return Membership::query()
            ->ensembleRestricted()
            ->with('user')
            ->emptyDobs()
            ->count();
    }

    private function getBirthdays()
    {
        return User::query()
            ->whereHas('memberships', fn($query) => $query->ensembleRestricted()->active())
            ->select('*')
            ->birthdays()
            ->get()
            ->append('birthday')
            ->sortBy('birthday');
    }

    private function getAttendanceSummary(Membership $singer): array|null
    {
        if (!auth()->user()?->can('viewAttendance', $singer)) {
            return null;
        }

        $eventType = EventType::where('title', 'Rehearsal')->first();
        if (!$eventType) {
            return null;
        }

        $recentEvents = Event::where('type_id', $eventType->id)
            ->where('start_date', '<=', now())
            ->orderByDesc('start_date')
            ->limit(8)
            ->get();

        $recentEventsCount = $recentEvents->count();

        if ($recentEventsCount === 0) {
            return null;
        }

        $attendedCount = $singer->attendances()
            ->whereIn('response', ['present', 'late'])
            ->whereIn('event_id', $recentEvents->pluck('id'))
            ->count();

        return [
            'attended' => $attendedCount,
            'total' => $recentEventsCount,
            'percentage' => $recentEventsCount > 0 ? round(($attendedCount / $recentEventsCount) * 100) : 0,
        ];
    }

    private function getRsvpSummary(Membership $singer): array|null
    {
        if (!auth()->user()?->can('viewAttendance', $singer)) {
            return null;
        }

        $eventType = EventType::where('title', 'Performance')->first();
        if (!$eventType) {
            return null;
        }

        $next8Events = Event::where('type_id', $eventType->id)
            ->where('start_date', '>', now())
            ->orderBy('start_date', 'asc')
            ->limit(8)
            ->get();

        if ($next8Events->isEmpty()) {
            return null;
        }

        $eventIds = $next8Events->pluck('id');
        $respondedRsvpCount = $singer->rsvps()
            ->whereIn('event_id', $eventIds)
            ->whereIn('response', ['yes', 'no'])
            ->count();

        return [
            'responded' => $respondedRsvpCount,
            'total' => $next8Events->count(),
            'percentage' => round(($respondedRsvpCount / $next8Events->count()) * 100),
        ];
    }

    private function getActivePolls(): \Illuminate\Support\Collection
    {
        $membership = auth()->user()?->membership;

        if (!$membership) {
            return new Collection();
        }

        return Poll::query()
            ->with(['options' => fn($q) => $q->withCount('votes')])
            ->where(function (Builder $query) {
                $query->whereNull('close_at')
                    ->orWhere('close_at', '>=', now());
            })
            ->where('is_closed', false)
        ->get()
        ->each(function (Poll $poll) use ($membership) {
            $poll->my_vote_option_ids = $poll->votes()
                ->where('membership_id', $membership->id)
                ->pluck('poll_option_id')
                ->toArray();
        })
        ->values();
    }
}
