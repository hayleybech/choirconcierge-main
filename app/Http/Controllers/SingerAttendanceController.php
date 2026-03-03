<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\EventType;
use App\Models\Membership;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SingerAttendanceController extends Controller
{
    public function __invoke(Membership $singer): Response
    {
        $this->authorize('view', $singer);

        $attendances = QueryBuilder::for(Attendance::class)
            ->where('membership_id', $singer->id)
            ->with(['event' => fn($query) => $query->with('type')])
            ->join('events', 'attendances.event_id', '=', 'events.id')
            ->allowedFilters([
                AllowedFilter::callback('type.id', fn($query, $value) => $query->whereIn('events.type_id', (array) $value)),
                AllowedFilter::callback('starts_after', fn($query, $value) => $query->where('events.start_date', '>=', $value)),
                AllowedFilter::callback('starts_before', fn($query, $value) => $query->where('events.start_date', '<=', $value)),
            ])
            ->orderBy('events.start_date', 'desc')
            ->select('attendances.*')
            ->get();

        return Inertia::render('Singers/Attendance/Index', [
            'singer' => $singer->load('user'),
            'attendances' => $attendances,
            'eventTypes' => EventType::all(),
        ]);
    }
}
