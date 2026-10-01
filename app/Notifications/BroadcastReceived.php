<?php

namespace App\Notifications;

use App\Models\UserGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BroadcastReceived extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $subject,
        private readonly string $body,
        private readonly UserGroup $group,
        private readonly int $mailLogId,
    )
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'broadcast-received',
            'subject' => $this->subject,
            'body' => $this->body,
            'url' => route('communications.show', [
                'tenant' => $this->group->tenant_id,
                'mail_log' => $this->mailLogId,
            ]),
            'group_id' => $this->group->id,
            'group_title' => $this->group->title,
            'tenant_id' => $this->group->tenant_id,
        ];
    }
}
