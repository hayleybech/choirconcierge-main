<?php

namespace App\Jobs;

use App\Enums\EmailCategory;
use App\Enums\ProductAnnouncementAudience;
use App\Models\MailLog;
use App\Services\ProductAnnouncementRecipients;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Str;

class QueueProductAnnouncement implements ShouldQueue
{
    use Queueable;

    /**
     * @param array<array{hashName: string, originalName: string, size: int}> $fileMeta
     */
    public function __construct(
        public string $audience,
        public string $category,
        public string $subject,
        public string $body,
        public int $mailLogId,
        public array $fileMeta,
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ProductAnnouncementRecipients $recipients): void
    {
        $audience = ProductAnnouncementAudience::from($this->audience);
        $category = EmailCategory::from($this->category);
        $queuedCount = 0;

        $recipients->query($audience, $category)
            ->select('users.id')
            ->chunkById(100, function ($users) use ($category, &$queuedCount): void {
                foreach ($users as $user) {
                    DeliverProductAnnouncement::dispatch(
                        $user->getKey(),
                        $this->subject,
                        $this->body,
                        $category->value,
                        $this->mailLogId,
                        $this->fileMeta,
                    );

                    $queuedCount++;
                }
            });

        if ($queuedCount === 0) {
            MailLog::find($this->mailLogId)?->events()->create([
                'status' => 'audience-empty',
                'context' => Str::limit($category->label(), 64),
            ]);
        }
    }
}
