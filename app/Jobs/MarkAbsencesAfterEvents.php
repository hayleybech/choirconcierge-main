<?php

namespace App\Jobs;

use App\Models\Attendance;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class MarkAbsencesAfterEvents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    /**
     * Automatically marks singers absent for events that ended in the last hour (with a grace period),
     * If at least some attendances were marked before the event ended.
     */
    public function handle(): void
    {
        $targeted_event_ids = Event::query()
            ->whereBetween('end_date', [now()->subHour()->subMinutes(30), now()])
                ->whereHas('attendances', function (Builder $query) {
                    $query->where('response', '!=', 'unknown');
                })
            ->pluck('id');

        Attendance::query()
            ->whereIn('event_id', $targeted_event_ids)
            ->where('response', '=', 'unknown')
            ->update(['response' => 'absent']);
    }
}
