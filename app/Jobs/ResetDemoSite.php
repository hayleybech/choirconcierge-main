<?php

namespace App\Jobs;

use App\Models\Enrolment;
use App\Models\Ensemble;
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
use Illuminate\Support\Facades\DB;
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
        DB::transaction(function() {
            $demo_old = Tenant::find('demo');

            // Delete any data in tables lacking "on delete cascade"
            $demo_old?->run(function() use ($demo_old) {
                SongAttachment::query()
                    ->whereIn('song_id', Song::query()
                        ->withoutGlobalScopes(['filterPending'])
                        ->withTrashed()
                        ->pluck('id'))
                    ->delete();

                Enrolment::query()
                    ->whereIn('ensemble_id', $demo_old->ensembles->pluck('id'))
                    ->delete();

                $demo_old->ensembles()->delete();
                $demo_old->members()->delete();
            });

            $demo_old?->delete();

            // Re-create demo tenant
            $demo = Tenant::create('demo', 'Hypothetical Harmony Pty Ltd', 'Australia/Perth');
            $demo->domains()->create(['domain' => 'demo']);
            $demo->ensembles()->updateOrCreate(['name' => 'Hypothetical Harmony']);

            // Re-upload tenant logo ("upload" - we're just copying the sample file)
            $logo_path = Storage::disk('global-local')->path('sample/'. 'demo-logo.png');
            $logo_hashname = Str::random(40).'.png';

            $demo->updateLogo($logo_path, $logo_hashname);
            $demo->ensembles()->first()->updateLogo($logo_path, $logo_hashname);
        });
    }

}
