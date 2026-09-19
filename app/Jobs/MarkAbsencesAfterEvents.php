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
        Event::query()
            ->with('tenant')
            ->whereBetween('end_date', [now()->subHour()->subMinutes(30), now()])
            ->whereHas('attendances', function (Builder $query) {
                $query->where('response', '!=', 'unknown');
            })
            ->get()
            ->groupBy('tenant_id')
            ->each(function ($events, $tenantId) {
                tenancy()->initialize($tenantId);

                $events->each(function (Event $event) {
                    Attendance::query()
                        ->where('event_id', $event->id)
                        ->where('response', '=', 'unknown')
                        ->update([
                            'response' => 'absent',
                            'source' => 'after-event',
                        ]);
                });

                tenancy()->end();
            });
    }
}
