<?php

use App\Navigation\UserNavigation;

test('user navigation returns route names without urls by default', function () {
    $data = (new UserNavigation())->get(true);

    expect($data)->toBeArray();
    expect($data[0])->toHaveKey('route', 'central.account.edit');
    expect($data[0])->not->toHaveKey('url');
});

test('user navigation adds urls while preserving route names for the api', function () {
    $data = (new UserNavigation())->get(true, forApi: true);

    expect($data)->toBeArray();
    expect($data[0])->toHaveKey('url');
    expect($data[0])->toHaveKey('route', 'central.account.edit');
    expect($data[0]['url'])->toContain('/app/account/edit');

    expect(collect($data)->firstWhere('name', 'Help (Email Us)'))->toBeNull();
});
