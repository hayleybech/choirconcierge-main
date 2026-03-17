<?php

namespace App\Notifications;

use App\Models\MailLog;
use App\Models\MailLogEvent;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

trait LogsToMailLog
{
    /**
     * Log the notification to the MailLog table.
     */
    public function log(string $targetId): void
    {
        $uniqueKey = class_basename($this) . '-' . $targetId;
        $uid = 'notification-' . $uniqueKey;

        // Ensure we don't log the same event twice
        if (MailLog::where('uid', $uid)->exists()) {
            return;
        }

        $notifiable = new \stdClass();
        $notifiable->id = 0; // Fake notifiable for generating mail content if needed

        $mailMessage = $this->toMail($notifiable);

        if (Auth::check()) {
            $fromAddress = Auth::user()->email;
            $fromName = Auth::user()->name;
        } else {
            $fromAddress = tenant('mail_from_address') ?? config('mail.from.address');
            $fromName = tenant('mail_from_name') ?? config('mail.from.name');
        }

        $mailLog = MailLog::create([
            'uid' => $uid,
            'from' => "{$fromName} <{$fromAddress}>",
            'to' => 'Everyone',
            'subject' => Str::limit($mailMessage->subject, 125),
            'body' => Str::limit($this->renderMailMessage($mailMessage), 4997),
            'has_attachments' => false, // System notifications currently don't have attachments
            'received_at' => now(),
        ]);

        if (tenant('id')) {
            $mailLog->tenants()->attach(tenant('id'));
        }

        MailLogEvent::create([
            'status' => 'notification-sent',
            'mail_log_id' => $mailLog->id,
            'context' => Str::title(Str::snake(class_basename($this), ' ')),
        ]);
    }

    /**
     * Render the MailMessage.
     */
    protected function renderMailMessage($mailMessage): string
    {
        try {
            // For markdown messages, we want the HTML body but it can be very large.
            // Laravel's render() returns the full HTML document.
            $html = (string) $mailMessage->render();

            // Extract the content from the "Email Body" section if possible to save space
            if (preg_match('/<!-- Email Body -->.*?<td class="content-cell"[^>]*>(.*?)<\/td>/s', $html, $matches)) {
                return trim($matches[1]);
            }

            return $html;
        } catch (\Exception $e) {
            return $mailMessage->introLines ? implode("\n", $mailMessage->introLines) : '';
        }
    }
}
