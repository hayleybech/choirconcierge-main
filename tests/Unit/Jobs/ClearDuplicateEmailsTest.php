<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ClearDuplicateEmails;
use App\Mail\IncomingMailbox;
use Mockery\MockInterface;
use Tests\TestCase;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Message;

class ClearDuplicateEmailsTest extends TestCase
{
    /** @test */
    public function it_deletes_emails_where_the_cc_contains_a_group_matching_the_to_field(): void
    {
        $mockMessage = $this->mock(Message::class, function (MockInterface $mock) {
            $mock->shouldReceive('getTo')->andReturn(new Attribute('to', [
                (object)['mail' => 'group@example.com', 'personal' => 'Group'],
            ]));
            $mock->shouldReceive('getCc')->andReturn(new Attribute('cc', [
                (object)['mail' => 'group@example.com', 'personal' => 'Group'],
            ]));

            $mock->shouldReceive('delete')->andReturn(true);
        });

        $this->mock(IncomingMailbox::class, function (MockInterface $mock) use ($mockMessage) {
            $mock->shouldReceive('getMessages')->andReturn(collect([$mockMessage]));
        });

        $this->app->make(ClearDuplicateEmails::class)->handle();

        $mockMessage->shouldHaveReceived('delete');
    }

    /** @test */
    public function it_does_not_delete_emails_where_the_cc_and_to_fields_dont_match(): void
    {
        $mockMessage = $this->mock(Message::class, function (MockInterface $mock) {
            $mock->shouldReceive('getTo')->andReturn(new Attribute('to', [
                (object)['mail' => 'group@example.com', 'personal' => 'Group'],
            ]));
            $mock->shouldReceive('getCc')->andReturn(new Attribute('cc', [
                (object)['mail' => 'other@example.com', 'personal' => 'Other'],
            ]));

            $mock->shouldNotReceive('delete');
        });

        $this->mock(IncomingMailbox::class, function (MockInterface $mock) use ($mockMessage) {
            $mock->shouldReceive('getMessages')->andReturn(collect([$mockMessage]));
        });

        $this->app->make(ClearDuplicateEmails::class)->handle();

        $mockMessage->shouldNotHaveReceived('delete');
    }

    /** @test */
    public function it_still_finds_matching_recipients_if_there_are_multiple_other_recipients(): void
    {
        $mockMessage = $this->mock(Message::class, function (MockInterface $mock) {
            $mock->shouldReceive('getTo')->andReturn(new Attribute('to', [
                (object)['mail' => 'group@example.com', 'personal' => 'Group'],
                (object)['mail' => 'blah@test.com', 'personal' => 'Blah'],
            ]));
            $mock->shouldReceive('getCc')->andReturn(new Attribute('cc', [
                (object)['mail' => 'group@example.com', 'personal' => 'Group'],
                (object)['mail' => 'other@example.com', 'personal' => 'Other'],
            ]));

            $mock->shouldReceive('delete')->andReturn(true);
        });

        $this->mock(IncomingMailbox::class, function (MockInterface $mock) use ($mockMessage) {
            $mock->shouldReceive('getMessages')->andReturn(collect([$mockMessage]));
        });

        $this->app->make(ClearDuplicateEmails::class)->handle();

        $mockMessage->shouldHaveReceived('delete');
    }
}