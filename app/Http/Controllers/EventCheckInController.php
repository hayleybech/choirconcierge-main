<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class EventCheckInController extends Controller
{
    public function index(Event $event, Request $request): Response
    {
        return Inertia::render('Events/Attendance/CheckIn', [
            'event' => $this->getEventInstanceByTime($event),
            'storeUrlSigned' => URL::temporarySignedRoute('events.check-ins.store', now()->addMinutes(5), ['event' => $event]),
        ]);
    }

    private function getEventInstanceByTime(Event $event)
    {
        if (!$event->is_repeating) {
            return $event;
        }


        /**
         * Get the instance from this series where:
         * -- the start or end date are today (one-day events or the 1st/last day of multi-day event)
         * -- or today is between start and end date (middle day of multi-day event)
         *
         * It's important we still match an event that's today but has already ended or hasn't started yet
         * Because we have to allow early or late check-in
         */
        $instanceToday = Event::query()
            ->where('repeat_parent_id', $event->id)
            ->where(fn(Builder $query) => $query
                ->where(fn(Builder $query) => $query
                    ->whereDate('start_date', '>=', Carbon::today()->startOfDay())
                    ->whereDate('start_date', '<=', Carbon::today()->endOfDay())
                )
                ->orWhere(fn(Builder $query) => $query
                    ->whereDate('end_date', '>=', Carbon::today()->startOfDay())
                    ->whereDate('end_date', '<=', Carbon::today()->endOfDay())
                )
                ->orWhere(fn(Builder $query) => $query
                    ->whereDate('start_date', '>=', Carbon::today()->startOfDay())
                    ->whereDate('end_date', '<=', Carbon::today()->endOfDay())
                )
            )
            ->orderBy('start_date')
            ->first();

        if ($instanceToday) {
            return $instanceToday;
        }

        // If no instance currently running return the next upcoming instance
        return Event::query()
            ->where('repeat_parent_id', $event->id)
            ->whereDay('start_date', '>=', Carbon::today())
            ->orderBy('start_date')
            ->firstOrFail();
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        Gate::denyIf(fn(User $user) => !today()->isBetween($event->start_date->startOfDay(), $event->end_date->endOfDay()), 'There is no event at this time. ');

        $event->attendances()
            ->updateOrCreate(
                ['membership_id' => $request->user()->membership->id],
                ['response' => self::getAttendanceByEvent($event)]
            );

        return redirect()
            ->back()
            ->with(['status' => 'Attendance recorded.']);
    }

    public static function getAttendanceByEvent(Event $event): string
    {
        $MARK_ABSENT_GRACE_PERIOD_MIN = 20;

        if ($event->call_time->isAfter(now())) {
            return 'present';
        }

        if ($event->call_time->addMinutes($MARK_ABSENT_GRACE_PERIOD_MIN)->isAfter(now())) {
            return 'late';
        }

        if ($event->end_date->isAfter(now())) {
            return 'late_deemed_absent';
        }
        return 'absent';
    }
}
