<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use function Pest\Laravel\actingAs;

uses(RefreshDatabase::class);

it('has a send email page', function () {
    actingAs($this->createUserWithRole('Music Team'));

    $this->get(the_tenant_route('groups.send.create'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/SendEmail'));
//            ->has('user'));
});
