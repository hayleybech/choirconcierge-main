<?php

use App\Models\MailLog;
use App\Models\MailLogEvent;
use App\Models\Tenant;
use Illuminate\Support\Facades\DB;

test('tracking only records once per email', function () {
    $tenant = Tenant::factory()->create();
    tenancy()->initialize($tenant);

    $mailLog = MailLog::create([
        'uid' => 'notification-unique-123',
        'from' => 'sender@example.com',
        'to' => 'receiver@example.com',
        'subject' => 'Unique Notification',
        'body' => 'Body',
        'received_at' => now(),
    ]);

    $email = 'receiver@example.com';
    $encryptedEmail = encrypt($email);

    // First open
    $this->get(route('mail-logs.open', [
        'tenant' => $tenant->id,
        'mail_log_uid' => $mailLog->uid,
        'email' => $encryptedEmail,
    ]))->assertStatus(200);

    expect($mailLog->events()->where('status', 'opened')->where('context', $email)->count())->toBe(1);

    // Second open (same email)
    $this->get(route('mail-logs.open', [
        'tenant' => $tenant->id,
        'mail_log_uid' => $mailLog->uid,
        'email' => $encryptedEmail,
    ]))->assertStatus(200);

    // Still only 1
    expect($mailLog->events()->where('status', 'opened')->where('context', $email)->count())->toBe(1);

    // Open with different email
    $otherEmail = 'other@example.com';
    $this->get(route('mail-logs.open', [
        'tenant' => $tenant->id,
        'mail_log_uid' => $mailLog->uid,
        'email' => encrypt($otherEmail),
    ]))->assertStatus(200);

    expect($mailLog->events()->where('status', 'opened')->where('context', $otherEmail)->count())->toBe(1);
    expect($mailLog->events()->where('status', 'opened')->count())->toBe(2);
});

test('mail log body strips tracking pixel when accessed via model', function () {
    $trackingUrl = 'https://choirconcierge.test/mail-log/open/notification-123/encrypted-email';
    $trackingPixel = '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none !important;" />';
    $body = "<h1>Hello</h1><p>This is a test email.</p>" . $trackingPixel;

    $mailLog = new MailLog([
        'uid' => 'notification-123',
        'from' => 'test@example.com',
        'to' => 'user@example.com',
        'subject' => 'Test Email',
        'body' => $body,
        'received_at' => now(),
    ]);

    // Accessing the body attribute should strip the tracking pixel
    expect($mailLog->body)->not->toContain($trackingUrl);
    expect($mailLog->body)->not->toContain('<img');
    expect($mailLog->body)->toContain('<h1>Hello</h1>');
    expect($mailLog->body)->toContain('<p>This is a test email.</p>');
});

test('mail log body strips tracking pixel when src is not first attribute', function () {
    $trackingUrl = 'https://choirconcierge.test/mail-log/open/notification-123/encrypted-email';
    $trackingPixel = '<img width="1" height="1" src="' . $trackingUrl . '" style="display:none !important;" />';
    $body = "<h1>Hello</h1>" . $trackingPixel;

    $mailLog = new MailLog(['body' => $body]);

    expect($mailLog->body)->not->toContain($trackingUrl);
    expect($mailLog->body)->not->toContain('<img');
    expect($mailLog->body)->toContain('<h1>Hello</h1>');
});

test('mail log body remains intact in the database', function () {
    $trackingUrl = 'https://choirconcierge.test/mail-log/open/notification-456/encrypted-email';
    $trackingPixel = '<img src="' . $trackingUrl . '" width="1" height="1" style="display:none !important;" />';
    $body = "<h1>Hello</h1><p>Another test.</p>" . $trackingPixel;

    $mailLog = MailLog::create([
        'uid' => 'notification-456',
        'from' => 'test@example.com',
        'to' => 'user@example.com',
        'subject' => 'Test Email 2',
        'body' => $body,
        'received_at' => now(),
    ]);

    // Check directly in database to ensure it's not stripped there
    $rawBody = DB::table('mail_logs')->where('id', $mailLog->id)->value('body');
    expect($rawBody)->toContain($trackingUrl);
    expect($rawBody)->toContain($trackingPixel);

    // Check via model accessor to ensure it IS stripped there
    expect($mailLog->fresh()->body)->not->toContain($trackingUrl);
});
