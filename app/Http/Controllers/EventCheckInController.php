<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Inertia\Inertia;
use Inertia\Response;

class EventCheckInController extends Controller
{
    public function index(Event $event, Request $request): Response
    {
        return Inertia::render('Events/Attendance/CheckIn', [
            'event' => $event,
            'storeUrlSigned' => URL::temporarySignedRoute('events.check-ins.store', now()->addMinutes(5), ['event' => $event]),
        ]);
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $event->attendances()
            ->updateOrCreate(
                ['membership_id' => $request->user()->membership->id],
                ['response' => self::getAttendanceByEvent($event)]
            );

        return redirect()
            ->back()
            ->with(['status' => 'Attendance recorded.']);
    }

    public static function getAttendanceByEvent(Event $event): string {
        $MARK_ABSENT_GRACE_PERIOD_MIN = 20;

        if($event->call_time->isAfter(now())) {
            return 'present';
        }

         if($event->call_time->addMinutes($MARK_ABSENT_GRACE_PERIOD_MIN)->isAfter(now())) {
            return 'late';
        }

        return 'late_deemed_absent';
    }
}
