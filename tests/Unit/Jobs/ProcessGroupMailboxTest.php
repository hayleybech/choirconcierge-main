<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessGroupMailbox;
use App\Mail\IncomingMailbox;
use App\Mail\IncomingMessage;
use App\Mail\WebklexImapMessageMailableAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;
use Webklex\IMAP\Message;

/**
 * @see \App\Jobs\ProcessGroupMailbox
 */
class ProcessGroupMailboxTest extends TestCase
{
	use RefreshDatabase;

	protected bool $tenancy = false;

	/** @test */
	public function handle_resends_a_messages(): void
	{
        $this->markTestIncomplete();

		// Arrange
        $mockMessage = $this->mock(Message::class, function (MockInterface $mock) {
            $mock->shouldReceive('getTo')->andReturn([(object) ['mail' => 'to@example.com', 'personal' => 'To']]);
            $mock->shouldReceive('getCc')->andReturn([(object) ['mail' => 'cc@example.com', 'personal' => 'Cc']]);
            $mock->shouldReceive('getFrom')->andReturn([(object) ['mail' => 'from@example.com', 'personal' => 'From']]);
            $mock->shouldReceive('getSubject')->andReturn('Subject');
            $mock->shouldReceive('getTextBody')->andReturn('Text Body');
            $mock->shouldReceive('getHTMLBody')->andReturn('HTML Text Body');
            $mock->shouldReceive('getAttachments')->andReturn([]);

            $mock->shouldReceive('delete');
        });
		$spyIncomingMessage = $this->spy(IncomingMessage::class);

		$this->mock(IncomingMailbox::class, function (MockInterface $mock) use ($mockMessage) {
			$mock->shouldReceive('getMessages')->andReturn(collect([$mockMessage]));
		});

        $mockAdapter = $this->mock('App\Mail\WebklexImapMessageMailableAdapter', function (MockInterface $mock) use ($spyIncomingMessage) {
            $mock->shouldReceive('toMailable')->andReturn($spyIncomingMessage);
        });

		// Act
		$processGroupMailbox = $this->app->make(ProcessGroupMailbox::class);
		$processGroupMailbox->handle();

		// Assert
        $mockAdapter->shouldHaveReceived('toMailable');
		$spyIncomingMessage->shouldHaveReceived('resendToGroups');

		$spyIncomingMessage->shouldHaveReceived('delete');
	}
}
