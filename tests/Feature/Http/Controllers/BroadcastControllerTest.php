<?php

use App\Jobs\SendEmailForGroup;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    Storage::fake();
});

it('has a send email page', function () {
    $user = $this->actingAsRole('Music Team');

    createGroup($user);

    $this->get(the_tenant_route('groups.broadcasts.create'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/Broadcasts/Create')
            ->has('lists', 1)
        );
});

it('dispatches a job to send the email', function () {
    $user = $this->actingAsRole('Music Team');

    $group = createGroup($user);

    $this->post(the_tenant_route('groups.broadcasts.store'), [
        'list' => $group->id,
        'subject' => 'this is a test',
        'body' => 'test body',
    ])->assertSessionHasNoErrors();

    Queue::assertPushed(SendEmailForGroup::class, function (SendEmailForGroup $job) use ($user, $group) {
        return $job->group->is($group)
            && $job->message->subject === 'this is a test'
            && $job->message->body === 'test body'
            && $job->message->hasFrom($user);
    });
});

function createGroup(User $user): UserGroup
{
    /** @var UserGroup $group */
    $group = UserGroup::factory()->create();
    $group->list_type = 'distribution';
    $group->sender_users()->attach($user);
    $group->save();
    return $group;
}