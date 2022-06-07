<?php

use App\Mail\CloneMessage;
use App\Mail\IncomingMessage;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('sends one copy per group recipient', function () {
    Mail::fake();

    $users = collect([]);

    $users->push(
        User::factory()->create([
            'email' => 'permitted@example.com',
        ]),
        User::factory()->create([
            'email' => 'recipient_1@example.com',
        ]),
        User::factory()->create([
            'email' => 'recipient_2@example.com',
        ]),
    );

    createTestTenants()[0]
        ->run(function () use ($users) {
            $group_expected = UserGroup::create([
                'title' => 'Music Team',
                'slug' => 'music-team',
                'list_type' => 'chat',
            ]);

            $group_expected->recipient_users()->attach($users->pluck('id'));
        });

    $message = (new IncomingMessage())
        ->to('music-team@test-tenant-1.'.central_domain())
        ->from('permitted@example.com')
        ->subject('Just a test');

    // Act
    CloneMessage::forGroup($message, UserGroup::first());

    // Assert
    Mail::assertSent(IncomingMessage::class, 3);
    Mail::assertSent(IncomingMessage::class, static function ($mail) use ($users) {
        return $mail->hasTo($users);
    });
});
