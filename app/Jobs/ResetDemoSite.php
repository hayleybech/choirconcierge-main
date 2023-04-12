<?php

namespace App\Jobs;

use App\Models\Song;
use App\Models\SongAttachment;
use App\Models\Tenant;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
     * @throws Exception
     */
    public function handle()
    {
        $demo_old = Tenant::find('demo');

        // Delete any data in tables lacking "on delete cascade"
        $demo_old?->run(function() {
            SongAttachment::query()
                ->whereIn('song_id', Song::query()
                    ->withoutGlobalScopes(['filterPending'])
                    ->withTrashed()
                    ->pluck('id'))
                ->delete();
        });

        $demo_old?->delete();

        // Re-create demo tenant
        $demo = Tenant::create('demo', 'Hypothetical Harmony', 'Australia/Perth');
        $demo->domains()->create(['domain' => 'demo']);

        // Re-upload tenant logo ("upload" - we're just copying the sample file)
        $logo_path = Storage::disk('global-local')->path('sample/'. 'demo-logo.png');
        $logo_hashname = Str::random(40).'.png';

        $demo->updateLogo($logo_path, $logo_hashname);
    }

}
