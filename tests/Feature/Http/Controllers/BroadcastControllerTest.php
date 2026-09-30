<?php

use App\Jobs\SendEmailForGroup;
use App\Mail\OrganisationBroadcast;
use App\Models\MailLog;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
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

    $this->get(the_tenant_route('communications.create'))
        ->assertOk()
        ->assertInertia(fn (AssertableInertia $page) => $page
            ->component('MailingLists/Broadcasts/Create')
            ->has('lists', 1)
        );
});

it('dispatches a job to send the email', function () {
    $user = $this->actingAsRole('Music Team');

    $group = createGroup($user);

    $this->post(the_tenant_route('communications.store'), [
        'list' => $group->id,
        'subject' => 'this is a test',
        'body' => 'test body',
    ])->assertRedirect(route('communications.index'));

    Queue::assertPushed(SendEmailForGroup::class, function (SendEmailForGroup $job) use ($user, $group) {
        return $job->group->is($group)
            && $job->group->relationLoaded('tenant')
            && $job->message->subject === 'this is a test'
            && $job->message->body === 'test body'
            && $job->message->hasFrom($user);
    });
});

it('loads the group tenant before handling the queued email', function () {
    Mail::fake();

    $user = $this->actingAsRole('Admin');
    $group = UserGroup::factory()->create();
    $message = new OrganisationBroadcast(
        $group->load('tenant'),
        'this is a test',
        'test body',
        $user,
        collect(),
        'test-uid',
        0,
    );

    $job = new SendEmailForGroup($message, $group->withoutRelations());
    $job->group->unsetRelation('tenant');
    MailLog::factory()->create(['uid' => 'test-uid']);

    $job->handle();

    expect($job->group->relationLoaded('tenant'))->toBeTrue();
});

it('stores attachments in temporary storage', function () {
    $user = $this->actingAsRole('Music Team');

    $group = createGroup($user);

    $files = [
        UploadedFile::fake()->create('test.txt'),
        UploadedFile::fake()->create('test2.txt'),
    ];

    $this->post(the_tenant_route('communications.store'), [
        'list' => $group->id,
        'subject' => 'this is a test',
        'body' => 'test body',
        'attachments' => $files,
    ])->assertRedirect(route('communications.index'));

    Storage::disk('temp')->assertExists("broadcasts/{$files[0]->hashName()}");
    Storage::disk('temp')->assertExists("broadcasts/{$files[1]->hashName()}");

    Queue::assertPushed(SendEmailForGroup::class, function (SendEmailForGroup $job) use ($files, $user, $group) {
        return $job->message->fileMeta[0]['originalName'] === $files[0]->getClientOriginalName()
            && $job->message->fileMeta[1]['originalName'] === $files[1]->getClientOriginalName()
            && $job->message->fileMeta[0]['hashName'] === $files[0]->hashName()
            && $job->message->fileMeta[1]['hashName'] === $files[1]->hashName();
    });
});

it('logs the size of the broadcast', function () {
    $user = $this->actingAsRole('Music Team');

    $group = createGroup($user);

    $file = UploadedFile::fake()->create('test.txt', 100); // 100 KB

    $body = 'test body';
    $expectedSize = (100 * 1024) + strlen($body);

    $this->post(the_tenant_route('communications.store'), [
        'list' => $group->id,
        'subject' => 'this is a test',
        'body' => $body,
        'attachments' => [$file],
    ])->assertRedirect(route('communications.index'));

    $this->assertDatabaseHas('mail_logs', [
        'subject' => 'this is a test',
        'size' => $expectedSize,
    ]);
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
