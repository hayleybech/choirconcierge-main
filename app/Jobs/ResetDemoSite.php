<?php

namespace App\Jobs;

use App\Models\SongAttachment;
use App\Models\Tenant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResetDemoSite implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $demo_old = Tenant::find('demo');

        // Delete any data in tables lacking "on delete cascade"
        $demo_old?->run(function() {
            SongAttachment::query()->delete();
        });

        $demo_old?->delete();

        // Re-create demo tenant
        $demo = Tenant::create('demo', 'Hypothetical Harmony', 'Australia/Perth');
        $demo->domains()->create(['domain' => 'demo']);
    }
}
