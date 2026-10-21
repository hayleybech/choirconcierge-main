<?php

use App\Jobs\ProcessGroupMailbox;
use App\Mail\IncomingMailbox;
use App\Mail\MessageTooLargeMessage;
use App\Mail\WebklexImapMessageMailableAdapter;
use App\Models\MailLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Support\AttachmentCollection;

test('it rejects emails larger than 5MB', function () {
    Mail::fake();

    // Create a mock for the Message
    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getUid')->andReturn('large-email-uid');
    $message->shouldReceive('getSize')->andReturn(6 * 1024 * 1024); // 6MB
    
    // Add mocks for isDuplicateEmail check
    $message->shouldReceive('getCc')->andReturn(null);
    
    $fromAddress = new Address((object) ['mail' => 'sender@example.com']);
    $message->shouldReceive('getFrom')->andReturn(new Attribute('from', [$fromAddress]));
    
    $toAddress = new Address((object) ['mail' => 'group@example.choirconcierge.com']);
    $message->shouldReceive('getTo')->andReturn(new Attribute('to', [
        $toAddress,
        'additional@example.com',
    ]));
    
    $message->shouldReceive('getSubject')->andReturn(new Attribute('subject', 'Large Email'));
    $message->shouldReceive('hasAttachments')->andReturn(true);
    $message->shouldReceive('getDate')->andReturn(new Attribute('date', Carbon::now()));
    $message->shouldReceive('delete')->once();

    // Create a mock for the IncomingMailbox
    $mailbox = Mockery::mock(IncomingMailbox::class);
    $mailbox->shouldReceive('getMessages')->andReturn(collect([$message]));

    // Execute the job
    (new ProcessGroupMailbox($mailbox))->handle();

    // Assert rejection email was sent
    Mail::assertSent(MessageTooLargeMessage::class, function ($mail) {
        return $mail->hasTo('sender@example.com');
    });

    // Assert MailLog entry was created
    $log = MailLog::where('uid', 'large-email-uid')->first();
    expect($log)->not->toBeNull();
    expect($log->body)->toContain('Message rejected: size too large');
    expect($log->to)->toBe('group@example.choirconcierge.com, additional@example.com');
    
    // Assert MailLogEvent was created
    expect($log->events()->where('status', 'rejected-too-large')->exists())->toBeTrue();
});

test('it processes emails smaller than 5MB normally', function () {
    Mail::fake();

    // Create a mock for the Message
    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getUid')->andReturn('normal-email-uid');
    $message->shouldReceive('getSize')->andReturn(1 * 1024 * 1024); // 1MB
    
    // WebklexImapMessageMailableAdapter and IncomingMessage need more methods
    $message->shouldReceive('hasAttachments')->andReturn(false);
    $message->shouldReceive('getDate')->andReturn(new Attribute('date', Carbon::now()));
    
    $message->shouldReceive('getTo')->andReturn(new Attribute('to'));
    $message->shouldReceive('getCc')->andReturn(null);
    $message->shouldReceive('getFrom')->andReturn(new Attribute('from'));
    
    $message->shouldReceive('getSubject')->andReturn(new Attribute('subject', 'Normal Email'));
    $message->shouldReceive('getTextBody')->andReturn('Body');
    $message->shouldReceive('getHTMLBody')->andReturn('<html>Body</html>');
    
    $attachmentCollection = Mockery::mock(AttachmentCollection::class);
    $attachmentCollection->shouldReceive('all')->andReturn([]);
    $message->shouldReceive('getAttachments')->andReturn($attachmentCollection);
    
    $message->shouldReceive('delete')->once();

    // Create a mock for the IncomingMailbox
    $mailbox = Mockery::mock(IncomingMailbox::class);
    $mailbox->shouldReceive('getMessages')->andReturn(collect([$message]));

    // We don't want to actually run the full resendToGroups logic here as it requires DB setup
    // But we want to see it doesn't trigger the rejection logic
    (new ProcessGroupMailbox($mailbox))->handle();

    Mail::assertNotSent(MessageTooLargeMessage::class);
});

test('the mailable adapter copies string recipients without a personal name', function () {
    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getUid')->andReturn('string-recipient-uid');
    $message->shouldReceive('hasAttachments')->andReturn(false);
    $message->shouldReceive('getDate')->andReturn(new Attribute('date', Carbon::now()));
    $message->shouldReceive('getSize')->andReturn(1024);
    $message->shouldReceive('getTo')->andReturn(new Attribute('to', ['to@example.com']));
    $message->shouldReceive('getCc')->andReturn(new Attribute('cc'));
    $message->shouldReceive('getFrom')->andReturn(new Attribute('from', [
        new Address((object) ['mail' => 'sender@example.com']),
    ]));
    $message->shouldReceive('getSubject')->andReturn(new Attribute('subject', 'Test message'));
    $message->shouldReceive('getTextBody')->andReturn('Body');
    $message->shouldReceive('getHTMLBody')->andReturn('<html>Body</html>');
    $message->shouldReceive('getAttachments')->andReturn(new AttachmentCollection([]));

    $mailable = (new WebklexImapMessageMailableAdapter($message))->toMailable();

    expect($mailable->to[0]['address'])->toBe('to@example.com')
        ->and($mailable->to[0]['name'])->toBe('');
});

test('it reports empty recipient addresses without treating them as duplicates', function () {
    Mail::fake();
    Log::spy();

    $emptyAddress = new Address((object) ['mail' => '']);
    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getUid')->andReturn('empty-recipient-uid');
    $message->shouldReceive('getSize')->andReturn(6 * 1024 * 1024);
    $message->shouldReceive('getCc')->andReturn(new Attribute('cc', [$emptyAddress]));
    $message->shouldReceive('getFrom')->andReturn(new Attribute('from', [
        new Address((object) ['mail' => 'sender@example.com']),
    ]));
    $message->shouldReceive('getTo')->andReturn(new Attribute('to', [
        new Address((object) ['mail' => 'group@example.com']),
        $emptyAddress,
    ]));
    $message->shouldReceive('getSubject')->andReturn(new Attribute('subject', 'Malformed recipients'));
    $message->shouldReceive('hasAttachments')->andReturn(false);
    $message->shouldReceive('getDate')->andReturn(new Attribute('date', Carbon::now()));
    $message->shouldReceive('delete')->once();

    $mailbox = Mockery::mock(IncomingMailbox::class);
    $mailbox->shouldReceive('getMessages')->andReturn(collect([$message]));

    (new ProcessGroupMailbox($mailbox))->handle();

    $log = MailLog::where('uid', 'empty-recipient-uid')->first();

    expect($log)->not->toBeNull()
        ->and($log->to)->toBe('group@example.com');

    Log::shouldHaveReceived('warning')
        ->with('Inbound message recipient has no usable email address.', [
            'uid' => 'empty-recipient-uid',
            'field' => 'cc',
            'recipient_index' => 0,
        ]);

    Log::shouldHaveReceived('warning')
        ->with('Inbound message recipient has no usable email address.', [
            'uid' => 'empty-recipient-uid',
            'field' => 'to',
            'recipient_index' => 1,
        ]);
});
