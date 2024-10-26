<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

class EventCalendarController extends Controller
{
    public function __invoke(Request $request): View|Response
    {
        $this->authorize('viewAny', Event::class);

        $month = Carbon::make($request->input('month', today()))->setTimezone(tenant()->timezone ?? 'Australia/Perth');

        return Inertia::render('Events/Calendar/Month', [
            'days' => $this->getEventsForMonth($month),
            'month' => $month,
        ]);
    }

    private function getEventsForMonth(Carbon $selectedMonth): Collection
    {
        $dates = $this->getDates($selectedMonth);

        $events = Event::query()
            ->whereDate('call_time', '>=', $dates->first()->clone()->utc())
            ->whereDate('call_time', '<', $dates->last()->clone()->addDays(2)->utc())
            ->withCount([
                'rsvps as going_count' => function ($query) {
                    $query->where('response', '=', 'yes');
                },
                'attendances as present_count' => function ($query) {
                    $query->whereIn('response', ['present', 'late']);
                },
            ])
            ->get();

        return $dates->map(fn (Carbon $date) => [
            'date' => $date,
            'isCurrentMonth' => $date->isSameMonth($selectedMonth),
            'isToday' => $date->isToday(),
            'isSelected' => false,
            'events' => $events->filter(fn(Event $event) => $date->isSameDay($event->call_time))->values()
        ]);
    }

    private function getDates(Carbon $startOfMonth): Collection
    {
        $startOfDisplay = $startOfMonth->clone()->startOfMonth()->startOfWeek();
        $endOfDisplay = $startOfMonth->clone()->endOfMonth()->endOfWeek();

        $dates = collect([]);
        for($date = $startOfDisplay->clone(); $date < $endOfDisplay; $date->addDay()) {
            $dates->push($date->clone());
        }
        return $dates;
    }
}
