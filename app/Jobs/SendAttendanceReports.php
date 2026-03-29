<?php

namespace App\Jobs;

use App\Mail\AttendanceReport;
use App\Models\Event;
use App\Models\Membership;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendAttendanceReports implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Event::query()
            ->with('tenant')
            ->whereBetween('end_date', [now()->subHour()->subMinutes(30), now()])
            ->whereHas('attendances', function (Builder $query) {
                $query->whereIn('source', ['qr-code', 'kiosk']);
            })
            ->get()
            ->groupBy('tenant_id')
            ->each(function ($events, $tenantId) {
                tenancy()->initialize($tenantId);

                $events->each(function (Event $event) {
                    $this->sendAttendanceReport($event);
                });

                tenancy()->end();
            });
    }

    protected function sendAttendanceReport(Event $event): void
    {
        Membership::with('user')->whereHas('roles', function ($query) {
            $query->whereJsonContains('abilities', 'attendances_view');
        })->get()->each(function ($member) use ($event) {
            Mail::to($member->user->email)->send(new AttendanceReport($event));
        });
    }
}
