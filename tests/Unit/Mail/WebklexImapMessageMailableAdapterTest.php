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

	/**
	 * @test
	 * @dataProvider recipientProvider
	 */
	public function copies_recipients($recipient_type, $count, $recipients): void
	{
		// Arrange
		$message = $this->mockMessage([$recipient_type => $recipients]);

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertCount($count, $mailable->$recipient_type);
		self::assertEquals($recipients[0]->mail, $mailable->$recipient_type[0]['address']);
		self::assertEquals($recipients[0]->personal, $mailable->$recipient_type[0]['name']);
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

	private function mockMessage(array $test_recipients = []): MockInterface
	{
		$recipients = array_merge(
			[
				'to' => [
					(object) [
						'mail' => 'to@example.com',
						'personal' => 'Name To',
					],
				],
				'cc' => [
					(object) [
						'mail' => 'cc@example.com',
						'personal' => 'Name Cc',
					],
				],
				'from' => [
					(object) [
						'mail' => 'to@example.com',
						'personal' => 'Name To',
					],
				],
			],
			$test_recipients,
		);
		return $this->mock(Message::class, function (MockInterface $mock) use ($recipients) {
			$mock->shouldReceive('getTo')->andReturn($recipients['to']);
			$mock->shouldReceive('getCc')->andReturn($recipients['cc']);
			$mock->shouldReceive('getFrom')->andReturn($recipients['from']);
			$mock->shouldReceive('getSubject')->andReturn('A Test Subject');
			$mock->shouldReceive('getTextBody')->andReturn('Hello');
			$mock->shouldReceive('getHTMLBody')->andReturn('<html>Hello</html>');
			$mock->shouldReceive('getAttachments')->andReturn([]);
		});
	}

	public function recipientProvider()
	{
		return [
			'single to' => [
				'recipient_type' => 'to',
				'count' => 1,
				'recipients' => [(object) ['personal' => 'Name To', 'mail' => 'to@example.com']],
			],
			'multiple to' => [
				'recipient_type' => 'to',
				'count' => 2,
				'recipients' => [
					(object) ['personal' => 'Name To 1', 'mail' => 'to_1@example.com'],
					(object) ['personal' => 'Name To 2', 'mail' => 'to_2@example.com'],
				],
			],
			'single cc' => [
				'recipient_type' => 'cc',
				'count' => 1,
				'recipients' => [(object) ['personal' => 'Name Cc', 'mail' => 'cc@example.com']],
			],
			'multiple cc' => [
				'recipient_type' => 'cc',
				'count' => 2,
				'recipients' => [
					(object) ['personal' => 'Name Cc 1', 'mail' => 'cc_1@example.com'],
					(object) ['personal' => 'Name Cc 2', 'mail' => 'cc_2@example.com'],
				],
			],
			'single from' => [
				'recipient_type' => 'from',
				'count' => 1,
				'recipients' => [(object) ['personal' => 'Name From', 'mail' => 'from@example.com']],
			],
			'multiple from' => [
				'recipient_type' => 'from',
				'count' => 2,
				'recipients' => [
					(object) ['personal' => 'Name From 1', 'mail' => 'from_1@example.com'],
					(object) ['personal' => 'Name From 2', 'mail' => 'from_2@example.com'],
				],
			],
		];
	}
}
