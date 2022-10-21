<?php

namespace Tests\Unit\Mail;

use App\Mail\IncomingMessage;
use App\Mail\NotPermittedSenderMessage;
use App\Models\Tenant;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * @see \App\Mail\IncomingMessage
 */
class IncomingMessageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $tenancy = false;

    /** @test */
    public function resendToGroups_with_one_group_resends_to_sender(): void
    {
        // Arrange
        Mail::fake();

        $this->createTestTenants()[0]
            ->run(function () {
                $group_expected = UserGroup::create([
                    'title' => 'Music Team',
                    'slug' => 'music-team',
                    'list_type' => 'chat',
                ]);
                $user1 = User::factory()->create([
                    'email' => 'permitted@example.com',
                ]);
                $group_expected->recipient_users()->attach([$user1->id]);
            });

        $message = (new IncomingMessage())
            ->to('music-team@test-tenant-1.'.central_domain())
            ->from('permitted@example.com')
            ->subject('Just a test');

        // Act
        $message->resendToGroups();

        // Assert
        Mail::assertNotSent(NotPermittedSenderMessage::class);
        Mail::assertSent(IncomingMessage::class, 1);
        Mail::assertSent(IncomingMessage::class, static function ($mail) {
            $mail->build();

            return $mail->hasFrom('music-team@test-tenant-1.'.central_domain()) &&
                $mail->hasReplyTo('permitted@example.com') &&
                $mail->hasTo('permitted@example.com');
        });
    }

    /** @test */
    public function resendToGroups_sends_rejection_email_when_not_permitted(): void
    {
        // Arrange
        Mail::fake();

        $this->createTestTenants()[0]
            ->run(function () {
                UserGroup::create([
                    'title' => 'Music Team',
                    'slug' => 'music-team',
                    'list_type' => 'chat',
                ]);
            });

        $message = (new IncomingMessage())
            ->to('music-team@test-tenant-1.'.central_domain())
            ->from('not-permitted@example.com')
            ->subject('Just a test');

        // Act
        $message->resendToGroups();

        // Assert
        Mail::assertNotSent(IncomingMessage::class);
        Mail::assertSent(NotPermittedSenderMessage::class, 1);
        Mail::assertSent(NotPermittedSenderMessage::class, static function ($mail) {
            $mail->build();

            return $mail->hasFrom('hello@test-tenant-1.'.central_domain()) &&
                $mail->hasTo('not-permitted@example.com');
        });
    }

    /** @test */
    public function resendToGroups_with_one_group_resends_to_group_members(): void
    {
        // Arrange
        Mail::fake();

        $users = collect([]);

        $users->push(
        // @todo merge these create statements
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

        $this->createTestTenants()[0]
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
        $message->resendToGroups();

        // Assert
        Mail::assertSent(IncomingMessage::class, 3);
        Mail::assertSent(IncomingMessage::class, static function ($mail) use ($users) {
            return $mail->hasTo($users);
        });
    }

    /** @test */
    public function resendToGroups_with_multiple_groups_from_same_tenant(): void
    {
        // Arrange
        Mail::fake();

        $this->createTestTenants()[0]
            ->run(function () {
                $sender_user = User::factory()->create([
                    'email' => 'sender@example.com',
                ]);
                $group_expected_1 = UserGroup::create([
                    'title' => 'Music Team',
                    'slug' => 'music-team',
                    'list_type' => 'chat',
                ]);
                $recipient_user_1 = User::factory()->create([
                    'email' => 'recipient-1@example.com',
                ]);
                $group_expected_1->recipient_users()->attach([$sender_user->id, $recipient_user_1->id]);

                $group_expected_2 = UserGroup::create([
                    'title' => 'Membership Team',
                    'slug' => 'membership-team',
                    'list_type' => 'chat',
                ]);
                $recipient_user_2 = User::factory()->create([
                    'email' => 'recipient-2@example.com',
                ]);
                $group_expected_2->recipient_users()->attach([$sender_user->id, $recipient_user_2->id]);
            });

        $message = (new IncomingMessage())
            ->to(['music-team@test-tenant-1.'.central_domain(), 'membership-team@test-tenant-1.'.central_domain()])
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $message->resendToGroups();

        // Assert
        Mail::assertNotSent(NotPermittedSenderMessage::class);
        Mail::assertSent(IncomingMessage::class, 4);
        Mail::assertSent(IncomingMessage::class, static function ($mail) {
            $mail->build();

            return $mail->hasFrom('music-team@test-tenant-1.'.central_domain()) &&
                $mail->hasReplyTo('sender@example.com') &&
                $mail->hasTo(['sender@example.com', 'recipient-1@example.com']);
        });
        Mail::assertSent(IncomingMessage::class, static function ($mail) {
            $mail->build();

            return $mail->hasFrom('membership-team@test-tenant-1.'.central_domain()) &&
                $mail->hasReplyTo('sender@example.com') &&
                $mail->hasTo(['sender@example.com', 'recipient-2@example.com']);
        });
    }

    /** @test */
    public function resendToGroups_with_multiple_groups_from_any_tenant(): void
    {
        // Arrange
        Mail::fake();

        list($tenant_1, $tenant_2) = $this->createTestTenants(2);

        $sender_user = User::factory()->create([
            'email' => 'sender@example.com',
        ]);
        $recipient_user_1 = User::factory()->create([
            'email' => 'recipient-1@example.com',
        ]);
        $recipient_user_2 = User::factory()->create([
            'email' => 'recipient-2@example.com',
        ]);

        $tenant_1->run(function () use ($recipient_user_1, $sender_user) {
            $group_expected_1 = UserGroup::create([
                'title' => 'Music Team',
                'slug' => 'music-team',
                'list_type' => 'chat',
            ]);

            $group_expected_1->recipient_users()->attach([$sender_user->id, $recipient_user_1->id]);
        });

        $tenant_2->run(function () use ($recipient_user_2, $sender_user) {
            $group_expected_2 = UserGroup::create([
                'title' => 'Membership Team',
                'slug' => 'membership-team',
                'list_type' => 'chat',
            ]);
            $group_expected_2->recipient_users()->attach([$sender_user->id, $recipient_user_2->id]);
        });

        $message = (new IncomingMessage())
            ->to(['music-team@test-tenant-1.'.central_domain(), 'membership-team@test-tenant-2.'.central_domain()])
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $message->resendToGroups();

        // Assert
        Mail::assertNotSent(NotPermittedSenderMessage::class);
        Mail::assertSent(IncomingMessage::class, 4);
        Mail::assertSent(IncomingMessage::class, static function ($mail) {
            $mail->build();

            return $mail->hasFrom('music-team@test-tenant-1.'.central_domain()) &&
                $mail->hasReplyTo('sender@example.com') &&
                $mail->hasTo(['sender@example.com', 'recipient-1@example.com']);
        });
        Mail::assertSent(IncomingMessage::class, static function ($mail) {
            $mail->build();

            return $mail->hasFrom('membership-team@test-tenant-2.'.central_domain()) &&
                $mail->hasReplyTo('sender@example.com') &&
                $mail->hasTo(['sender@example.com', 'recipient-2@example.com']);
        });
    }

    /** @test */
    public function getMatchingGroups_matches_one_group(): void
    {
        // Arrange
        $this->createTestTenants()[0]
            ->run(
                fn () => UserGroup::create([
                    'title' => 'Test Group',
                    'slug' => 'test-group',
                    'list_type' => 'chat',
                ]),
            );

        $message = (new IncomingMessage())
            ->to('test-group@test-tenant-1.'.central_domain())
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(1, $groups_found);
    }

    /** @test */
    public function getMatchingGroups_matches_groups_from_the_same_tenant(): void
    {
        // Arrange
        $this->createTestTenants()[0]
            ->run(function () {
                UserGroup::create([
                    'title' => 'Test Group 1',
                    'slug' => 'test-group-1',
                    'list_type' => 'chat',
                ]);
                UserGroup::create([
                    'title' => 'Test Group 2',
                    'slug' => 'test-group-2',
                    'list_type' => 'chat',
                ]);
            });

        $message = (new IncomingMessage())
            ->to(['test-group-1@test-tenant-1.'.central_domain(), 'test-group-2@test-tenant-1.'.central_domain()])
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(2, $groups_found);
    }

    /** @test */
    public function getMatchingGroups_matches_groups_in_any_recipient_field(): void
    {
        // Arrange
        $this->createTestTenants()[0]
            ->run(function () {
                UserGroup::create([
                    'title' => 'Test Group 1',
                    'slug' => 'test-group-1',
                    'list_type' => 'chat',
                ]);
                UserGroup::create([
                    'title' => 'Test Group 2',
                    'slug' => 'test-group-2',
                    'list_type' => 'chat',
                ]);
                UserGroup::create([
                    'title' => 'Test Group 3',
                    'slug' => 'test-group-3',
                    'list_type' => 'chat',
                ]);
            });

        $message = (new IncomingMessage())
            ->to('test-group-1@test-tenant-1.'.central_domain())
            ->cc('test-group-2@test-tenant-1.'.central_domain())
            ->bcc('test-group-3@test-tenant-1.'.central_domain())
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(3, $groups_found);
    }

    /** @test */
    public function getMatchingGroups_matches_groups_in_any_position_in_the_recipient_field(): void
    {
        // Arrange
        $this->createTestTenants()[0]
            ->run(function () {
                UserGroup::create([
                    'title' => 'Test Group 1',
                    'slug' => 'test-group-1',
                    'list_type' => 'chat',
                ]);
                UserGroup::create([
                    'title' => 'Test Group 2',
                    'slug' => 'test-group-2',
                    'list_type' => 'chat',
                ]);
                UserGroup::create([
                    'title' => 'Test Group 3',
                    'slug' => 'test-group-3',
                    'list_type' => 'chat',
                ]);
            });

        $message = (new IncomingMessage())
            ->to(['dummy@example.com', 'test-group-1@test-tenant-1.'.central_domain()])
            ->cc(['dummy@example.com', 'test-group-2@test-tenant-1.'.central_domain()])
            ->bcc(['dummy@example.com', 'test-group-3@test-tenant-1.'.central_domain()])
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(3, $groups_found);
    }

    /** @test */
    public function getMatchingGroups_matches_groups_from_any_tenant(): void
    {
        // Arrange
        list($tenant_1, $tenant_2) = $this->createTestTenants(2);

        $tenant_1->run(function () {
            UserGroup::create([
                'title' => 'Test Group 1',
                'slug' => 'test-group-1',
                'list_type' => 'chat',
            ]);
        });
        $tenant_2->run(function () {
            UserGroup::create([
                'title' => 'Test Group 2',
                'slug' => 'test-group-2',
                'list_type' => 'chat',
            ]);
        });

        $message = (new IncomingMessage())
            ->to(['test-group-1@test-tenant-1.'.central_domain(), 'test-group-2@test-tenant-2.'.central_domain()])
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(2, $groups_found);
    }

    /** @test */
    public function getMatchingGroups_only_returns_exact_matches(): void
    {
        // Arrange
        $this->createTestTenants()[0]
            ->run(function () {
                UserGroup::create([
                    'title' => 'All Members',
                    'slug' => 'all-members',
                    'list_type' => 'chat',
                ]);
            });

        $message = (new IncomingMessage())
            ->to(['al@test-tenant-1.'.central_domain()])
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(0);

        // Assert
        self::assertCount(0, $groups_found);
    }

    /** @test */
    public function getMatchingGroups_checks_the_tenant_slug_before_matching(): void
    {
        // Arrange
        $this->createTestTenants()[0]
            ->run(function () {
                UserGroup::create([
                    'title' => 'Test Group 1',
                    'slug' => 'test-group-1',
                    'list_type' => 'chat',
                ]);
                UserGroup::create([
                    'title' => 'Test Group 2',
                    'slug' => 'test-group-2',
                    'list_type' => 'chat',
                ]);
            });

        $message = (new IncomingMessage())
            ->to(['test-group-1@test-tenant-1.'.central_domain(), 'test-group-2@dummy-tenant.'.central_domain()])
            ->from('sender@example.com')
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(1, $groups_found);
    }

    /** @test */
    public function getMatchingGroups_rejects_groups_that_are_in_both_the_cc_and_from(): void
    {
        // Arrange
        $this->createTestTenants()[0]
            ->run(function () {
                UserGroup::create([
                    'title' => 'Test Group',
                    'slug' => 'test-group',
                    'list_type' => 'chat',
                ]);
            });

        $message = (new IncomingMessage())
            ->to('dummy@example.com')
            ->cc('test-group@test-tenant-1.'.central_domain())
            ->from('test-group@test-tenant-1.'.central_domain())
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(0, $groups_found);
    }

    /** @test */
    public function getGroupByEmail_skips_special_emails(): void
    {
        $message = (new IncomingMessage())
            ->to('undisclosed-recipients:;')
            ->cc('test-group@test-tenant-1.'.central_domain())
            ->from('test-group@test-tenant-1.'.central_domain())
            ->subject('Just a test');

        // Act
        $groups_found = $message->getMatchingGroups()->flatten(1);

        // Assert
        self::assertCount(0, $groups_found);
    }

    /**
     * @param int $numTenants
     * @return Collection<Tenant>
     */
    private function createTestTenants(int $numTenants = 1): Collection
    {
        return Collection::times($numTenants, function ($index) {
            return [
                'test-tenant-'.$index,
                'Test Tenant '.$index,
                'Australia/Perth',
            ];
        })
            ->map(function ($data) {
                $tenant = Tenant::create(...$data);
                $tenant->domains()->create(['domain' => $tenant->id]);

                tenancy()->end();

                return $tenant;
            });
    }
}
