<?php

use App\Jobs\ClearTemporaryBroadcastFiles;
use Carbon\Carbon;

it('deletes all attachments older than 1 day', function () {
    Storage::fake('temp')->put('broadcasts/delete-me.txt', '');

    Carbon::setTestNow(now()->addDay()->addHour());

    ClearTemporaryBroadcastFiles::dispatch();

    Storage::disk('temp')->assertMissing('broadcasts/delete-me.txt');
});

it('does not delete files newer than 1 day', function () {
    Storage::fake('temp')->put('broadcasts/keep-me.txt', '');

    ClearTemporaryBroadcastFiles::dispatch();

    Storage::disk('temp')->assertExists('broadcasts/keep-me.txt');
});
