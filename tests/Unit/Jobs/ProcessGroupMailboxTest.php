<?php

namespace Tests\Unit\Jobs;

use App\Jobs\ProcessGroupMailbox;
use App\Mail\IncomingMailbox;
use App\Mail\IncomingMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;

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
		// Arrange
		$message = $this->spy(IncomingMessage::class);

		$this->mock(IncomingMailbox::class, function (MockInterface $mock) use ($message) {
			$mock->shouldReceive('getMessages')->andReturn(collect([$message]));
		});

		// Act
		$processGroupMailbox = $this->app->make(ProcessGroupMailbox::class);
		$processGroupMailbox->handle();

		// Assert
		$message->shouldHaveReceived('resendToGroups');
	}
}
