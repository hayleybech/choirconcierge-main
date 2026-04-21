<?php

namespace App\Notifications;

use App\Models\MailLog;
use App\Models\MailLogEvent;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

trait LogsToMailLog
{
    /**
     * Get the UID for the MailLog entry.
     */
    public function getMailLogUid(string $targetId): string
    {
        $uniqueKey = class_basename($this) . '-' . $targetId;
        return 'notification-' . $uniqueKey;
    }

    /**
     * Get the tracking pixel URL for the notification.
     */
    public function getTrackingPixel(string $targetId, $notifiable): string
    {
        $uid = $this->getMailLogUid($targetId);
        $email = $notifiable->email ?? ($notifiable->user->email ?? null);

        if (!$email) {
            return '';
        }

        $trackingPixelUrl = route('mail-logs.open', [
            'mail_log_uid' => $uid,
            'email' => encrypt($email),
            'tenant' => tenant('id'),
        ]);

        return '<img src="' . $trackingPixelUrl . '" width="1" height="1" style="display:none !important;" />';
    }

    /**
     * Log the notification to the MailLog table.
     */
    public function log(string $targetId, $notifiable = null): void
    {
        $uid = $this->getMailLogUid($targetId);

        // Ensure we don't log the same event twice
//        if (MailLog::where('uid', $uid)->exists()) {
//            return;
//        }

        if (!$notifiable) {
            $notifiable = new \stdClass();
            $notifiable->id = 0; // Fake notifiable for generating mail content if needed
        }

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
            'from' => Str::limit("{$fromName} <{$fromAddress}>", 254),
            'to' => 'Everyone',
            'subject' => Str::limit($mailMessage->subject, 125),
            'body' => $this->renderMailMessage($mailMessage),
            'has_attachments' => false, // System notifications currently don't have attachments
            'received_at' => now(),
        ]);

        if ($notifiable instanceof User && $notifiable->default_tenant_id) {
            $mailLog->tenants()->attach($notifiable->default_tenant_id);
        } elseif (tenant('id')) {
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

            return $html;

            // Extract the content from the "Email Body" section if possible to save space
//            if (preg_match('/<!-- Email Body -->.*?<td class="content-cell"[^>]*>(.*?)<\/td>/s', $html, $matches)) {
//                $content = trim($matches[1]);
//                // Wrap in a div to ensure base layout styles if needed
//                return '<div class="mail-log-content">' . $content . '</div>';
//            }
//
//            return $html;
        } catch (\Exception $e) {
            return $mailMessage->introLines ? implode("\n", $mailMessage->introLines) : '';
        }
    }
}
