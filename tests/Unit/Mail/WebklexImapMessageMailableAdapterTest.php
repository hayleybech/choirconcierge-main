<?php

namespace Tests\Unit\Mail;

use App\Mail\IncomingMessage;
use App\Mail\WebklexImapMessageMailableAdapter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use Tests\TestCase;
use Webklex\IMAP\Message;

/**
 * @see \App\Mail\WebklexImapMessageMailableAdapter
 */
class WebklexImapMessageMailableAdapterTest extends TestCase
{
	use RefreshDatabase;

	protected bool $tenancy = false;

	/** @test */
	public function converts_message_to_mailable(): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertInstanceOf(IncomingMessage::class, $mailable);
	}

	/** @test */
	public function copies_to(): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertCount(1, $mailable->to);
		self::assertEquals('to@example.com', $mailable->to[0]['address']);
		self::assertEquals('Name To', $mailable->to[0]['name']);
	}

	/** @test */
	public function copies_cc(): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertCount(1, $mailable->cc);
		self::assertEquals('cc@example.com', $mailable->cc[0]['address']);
		self::assertEquals('Name Cc', $mailable->cc[0]['name']);
	}

	/** @test */
	public function copies_from(): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertCount(1, $mailable->from);
		self::assertEquals('from@example.com', $mailable->from[0]['address']);
		self::assertEquals('Name From', $mailable->from[0]['name']);
	}

	/** @test */
	public function copies_subject(): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertEquals('A Test Subject', $mailable->subject);
	}

	/** @test */
	public function copies_text_body(): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertEquals('Hello', $mailable->content_text);
	}

	/** @test */
	public function copies_html_body(): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertEquals('<html>Hello</html>', $mailable->content_html);
	}

	private function mockMessage(): MockInterface
	{
		return $this->mock(Message::class, function (MockInterface $mock) {
			$mock->shouldReceive('getTo')
				->andReturn([(object) [
					'mail'  => 'to@example.com',
					'personal' => 'Name To'
				]]);
			$mock->shouldReceive('getCc')
				->andReturn([(object) [
					'mail' => 'cc@example.com',
					'personal' => 'Name Cc'
				]]);
			$mock->shouldReceive('getFrom')
				->andReturn([(object) [
					'mail' => 'from@example.com',
					'personal' => 'Name From',
				]]);
			$mock->shouldReceive('getSubject')
				->andReturn('A Test Subject');
			$mock->shouldReceive('getTextBody')
				->andReturn('Hello');
			$mock->shouldReceive('getHTMLBody')
				->andReturn('<html>Hello</html>');
			$mock->shouldReceive('getAttachments')
				->andReturn([]);
		});
	}
}
