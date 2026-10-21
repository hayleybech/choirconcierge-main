<?php

namespace App\Jobs;

use App\Enums\EmailCategory;
use App\Mail\ProductAnnouncement;
use App\Models\MailLog;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class DeliverProductAnnouncement implements ShouldQueue
{
    use Queueable;

    /**
     * @param array<array{hashName: string, originalName: string, size: int}> $fileMeta
     */
    public function __construct(
        public int $userId,
        public string $subject,
        public string $body,
        public string $category,
        public int $mailLogId,
        public array $fileMeta,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $recipient = User::query()
            ->without(['media', 'membership.roles', 'membership.status'])
            ->find($this->userId);
        $category = EmailCategory::tryFrom($this->category);

        if ($recipient === null || $category === null) {
            return;
        }

        if (! $recipient->hasEmailPreference($category)) {
            MailLog::find($this->mailLogId)?->events()->create([
                'status' => 'announcement-skipped',
                'context' => Str::limit($recipient->email, 64),
            ]);

            return;
        }

        Mail::send(new ProductAnnouncement($recipient, $this->subject, $this->body, $category, $this->fileMeta));

        MailLog::find($this->mailLogId)?->events()->create([
            'status' => 'announcement-sent',
            'context' => Str::limit($recipient->email, 64),
        ]);
    }
}
