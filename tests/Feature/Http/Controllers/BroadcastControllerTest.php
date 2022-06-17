<?php

use App\Jobs\SendEmailForGroup;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Inertia\Testing\AssertableInertia;

uses(RefreshDatabase::class);

beforeEach(function () {
    Queue::fake();
    Storage::fake('temp');
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

it('stores attachments in temporary storage', function () {
    $user = $this->actingAsRole('Music Team');

    $group = createGroup($user);

    $files = [
        UploadedFile::fake()->create('test.txt'),
        UploadedFile::fake()->create('test2.txt'),
    ];

    $this->post(the_tenant_route('groups.broadcasts.store'), [
        'list' => $group->id,
        'subject' => 'this is a test',
        'body' => 'test body',
        'attachments' => $files,
    ])->assertSessionHasNoErrors();

    Storage::disk('temp')->assertExists("broadcasts/{$files[0]->hashName()}");
    Storage::disk('temp')->assertExists("broadcasts/{$files[1]->hashName()}");

    Queue::assertPushed(SendEmailForGroup::class, function (SendEmailForGroup $job) use ($files, $user, $group) {
        return $job->message->fileMeta[0]['originalName'] === $files[0]->getClientOriginalName()
            && $job->message->fileMeta[1]['originalName'] === $files[1]->getClientOriginalName()
            && $job->message->fileMeta[0]['hashName'] === $files[0]->hashName()
            && $job->message->fileMeta[1]['hashName'] === $files[1]->hashName();
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