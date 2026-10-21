<?php

use App\Jobs\ClearDuplicateEmails;
use App\Mail\IncomingMailbox;
use Illuminate\Support\Facades\Log;
use Webklex\PHPIMAP\Address;
use Webklex\PHPIMAP\Attribute;
use Webklex\PHPIMAP\Message;

test('it reports empty addresses without deleting a message as a duplicate', function () {
    Log::spy();

    $emptyAddress = new Address((object) ['mail' => '']);
    $message = Mockery::mock(Message::class);
    $message->shouldReceive('getUid')->andReturn('empty-duplicate-uid');
    $message->shouldReceive('getCc')->andReturn(new Attribute('cc', [$emptyAddress]));
    $message->shouldReceive('getTo')->andReturn(new Attribute('to', [$emptyAddress]));
    $message->shouldNotReceive('delete');

    $mailbox = Mockery::mock(IncomingMailbox::class);
    $mailbox->shouldReceive('getMessages')->with(true)->andReturn(collect([$message]));

    (new ClearDuplicateEmails($mailbox))->handle();

    Log::shouldHaveReceived('warning')
        ->with('Inbound message recipient has no usable email address.', [
            'uid' => 'empty-duplicate-uid',
            'field' => 'cc',
            'recipient_index' => 0,
        ]);

    Log::shouldHaveReceived('warning')
        ->with('Inbound message recipient has no usable email address.', [
            'uid' => 'empty-duplicate-uid',
            'field' => 'to',
            'recipient_index' => 0,
        ]);
});
