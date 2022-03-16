<?php

namespace App\Providers;

use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxFilesystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('dropbox', function ($app, $config) {
            $adapter = new DropboxAdapter(new DropboxClient($config['accessToken']));

            return new FilesystemAdapter(
                new Filesystem($adapter, $config),
                $adapter,
                $config

            );
        });
    }

    public function register()
    {
        //
    }
}
