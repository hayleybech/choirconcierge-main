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
	public function copies_recipients($recipient_type, $count, $addresses, $names): void
	{
		// Arrange
		$message = $this->mockMessage();

		// Act
		$mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

		// Assert
		self::assertCount($count, $mailable->$recipient_type);
		self::assertEquals($addresses[0], $mailable->$recipient_type[0]['address']);
		self::assertEquals($names[0], $mailable->$recipient_type[0]['name']);
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

	public function recipientProvider(){
	    return [
	        'single to' => [
	            'recipient_type' => 'to',
	            'count' => 1,
                'addresses' => ['to@example.com'],
                'name'  => ['Name To'],
            ],
            'single cc' => [
                'recipient_type' => 'cc',
                'count' => 1,
                'addresses' => ['cc@example.com'],
                'name'  => ['Name Cc'],
            ],
            'single from' => [
                'recipient_type' => 'from',
                'count' => 1,
                'addresses' => ['from@example.com'],
                'name'  => ['Name From'],
            ],
        ];
    }
}
