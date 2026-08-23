<?php

use App\Jobs\ProcessGroupMailbox;
use App\Mail\IncomingMailbox;
use App\Mail\MessageTooLargeMessage;
use App\Models\MailLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Webklex\PHPIMAP\Message;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Support\AttachmentCollection;

test('it rejects emails larger than 5MB', function () {
    Mail::fake();

    // Create a mock for the Message
    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getUid')->andReturn('large-email-uid');
    $message->shouldReceive('getSize')->andReturn(6 * 1024 * 1024); // 6MB
    
    // Add mocks for isDuplicateEmail check
    $message->shouldReceive('getCc')->andReturn(null);
    
    $fromAddress = Mockery::mock(Address::class);
    $fromAddress->mail = 'sender@example.com';
    $fromCollection = collect([$fromAddress]);
    $message->shouldReceive('getFrom')->andReturn($fromCollection);
    
    $toAddress = Mockery::mock(Address::class);
    $toAddress->mail = 'group@example.choirconcierge.com';
    $toCollection = collect([$toAddress]);
    $message->shouldReceive('getTo')->andReturn($toCollection);
    
    $message->shouldReceive('getSubject')->andReturn(collect(['Large Email']));
    $message->shouldReceive('hasAttachments')->andReturn(true);
    $message->shouldReceive('getDate')->andReturn(collect([Carbon::now()]));
    $message->shouldReceive('getSize')->andReturn(6 * 1024 * 1024);
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
    $message->shouldReceive('getDate')->andReturn(collect([Carbon::now()]));
    
    $message->shouldReceive('getTo')->andReturn(collect([]));
    $message->shouldReceive('getCc')->andReturn(null);
    $message->shouldReceive('getFrom')->andReturn(collect([]));
    
    $message->shouldReceive('getSubject')->andReturn(collect(['Normal Email']));
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
