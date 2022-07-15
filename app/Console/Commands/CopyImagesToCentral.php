<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use File;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class CopyImagesToCentral extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'once-off:copy-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Tenant::all()->each->run(function () {
            collect(Storage::disk('public')->directories())
                ->filter(fn (string $directory) => Media::find($directory))
                ->each(function (string $directory) {
                    File::copyDirectory(
                        Storage::disk('public')->path($directory),
                        Storage::disk('global-public')->path($directory),
                    );

                    Media::query()->where('id', $directory)->update(['disk' => 'global-public', 'conversions_disk' => 'global-public']);
                });
        });

        return 0;
    }
}
