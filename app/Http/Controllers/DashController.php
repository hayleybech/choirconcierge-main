<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventType;
use App\Models\Membership;
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
            'attendanceSummary' => $this->getAttendanceSummary(auth()?->user()?->membership),
            'rsvpSummary' => $this->getRsvpSummary(auth()?->user()?->membership),
            'performanceTypeId' => EventType::where('title', 'Performance')->first()?->id,
        ]);
    }

    private function getMemberversaries()
    {
        return Membership::query()
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
            ->whereIn('type_id',tenant('widgets_upcoming_events_categories') ?? EventType::all()->pluck('id'))
            ->orderBy('call_time')
            ->get()
            ->append(['my_rsvp']);
    }

    private function getEventCategories(): \Illuminate\Support\Collection
    {
        if(! auth()->user()->can('create', Event::class)) {
            return collect();
        }
        return EventType::query()
            ->select(['id', 'title'])
            ->get()
            ->values();
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
            ->select('*')
            ->birthdays()
            ->get()
            ->append('birthday')
            ->sortBy('birthday');
    }

    private function getAttendanceSummary(Membership $singer): array|null
    {
        if (! auth()->user()?->can('viewAttendance', $singer)) {
            return null;
        }

        $eightWeeksAgo = now()->subWeeks(8);
        $eventType = EventType::where('title', 'Rehearsal')->first();
        if (!$eventType) {
            return null;
        }

        $recentEventsCount = Event::where('type_id', $eventType->id)
            ->where('start_date', '>=', $eightWeeksAgo)
            ->where('start_date', '<=', now())
            ->count();

        $attendedCount = $singer->attendances()
            ->whereIn('response', ['present', 'late'])
            ->whereHas('event', function ($query) use ($eightWeeksAgo, $eventType) {
                $query->where('type_id', $eventType->id)
                    ->where('start_date', '>=', $eightWeeksAgo)
                    ->where('start_date', '<=', now());
            })
            ->count();

        return [
            'attended' => $attendedCount,
            'total' => $recentEventsCount,
            'percentage' => $recentEventsCount > 0 ? round(($attendedCount / $recentEventsCount) * 100) : 0,
        ];
    }

    private function getRsvpSummary(Membership $singer): array|null
    {
        if (! auth()->user()?->can('viewAttendance', $singer)) {
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
}
