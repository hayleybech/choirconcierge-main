<?php

use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('does not seed dummy data for a local tenant', function () {
    $this->seed(DatabaseSeeder::class);

    expect(User::query()->count())->toBeLessThan(30);
});
