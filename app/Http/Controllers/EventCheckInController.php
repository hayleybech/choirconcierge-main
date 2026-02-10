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

        // Return the next upcoming instance
        return Event::query()
            ->where('repeat_parent_id', $event->id)
            ->whereDate('start_date', '>=', Carbon::today())
            ->orderBy('start_date')
            ->firstOrFail();
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'event_id' => 'required|exists:events,id'
        ]);

        $event = Event::findOrFail($request->input('event_id'));

        Gate::denyIf(
            fn(User $user) => !today()->isBetween(
                $event->start_date->startOfDay(),
                $event->end_date->endOfDay()
            ),
            'There is no event at this time. '
        );

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
