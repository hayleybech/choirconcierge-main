<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client as DropboxClient;
use Spatie\FlysystemDropbox\DropboxAdapter;
use Storage;

class DropboxFilesystemServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new DropboxClient($config['accessToken']);

            return new Filesystem(new DropboxAdapter($client));
        });
    }

    public function register()
    {
        //
    }
}
