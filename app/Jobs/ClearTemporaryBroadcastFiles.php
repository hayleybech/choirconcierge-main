<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\FileAttributes;
use League\Flysystem\StorageAttributes;

class ClearTemporaryBroadcastFiles implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        collect(Storage::disk('temp')->listContents('broadcasts'))
            ->filter(fn (StorageAttributes $file) => $file->isFile() && $file->lastModified() < now()->subDay()->getTimestamp())
            ->each(fn (FileAttributes $file) => Storage::disk('temp')->delete($file->path()));
    }
}
