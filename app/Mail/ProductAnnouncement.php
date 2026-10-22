<?php

namespace App\Mail;

use App\Enums\EmailCategory;
use App\Models\User;
use App\Services\EmailPreferenceLinks;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ProductAnnouncement extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array<array{hashName: string, originalName: string, size: int}> $fileMeta
     */
    public function __construct(
        public User $recipient,
        string $subject,
        public string $body,
        public EmailCategory $category,
        public array $fileMeta,
    ) {
        $this->to($recipient->email, $recipient->name);
        $this->from('hayley@choirconcierge.com', 'Choir Concierge');
        $this->replyTo('hayley@choirconcierge.com', 'Choir Concierge');
        $this->subject($subject);
    }

    public function build(): static
    {
        foreach ($this->fileMeta as $file) {
            $this->attachFromStorageDisk('temp', 'broadcasts/' . $file['hashName'], $file['originalName']);
        }

        $emailPreferenceLinks = app(EmailPreferenceLinks::class)->make($this->recipient, $this->category);

        return $this->markdown('emails.product-announcement', [
            'body' => $this->body,
            'emailPreferenceLinks' => $emailPreferenceLinks,
        ]);
    }
}
