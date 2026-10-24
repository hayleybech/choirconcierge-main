<?php

namespace App\Mail;

use App\Enums\EmailCategory;
use App\Models\User;
use App\Models\UserGroup;
use App\Services\EmailPreferenceLinks;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use LogicException;

class OrganisationBroadcast extends Mailable implements Loggable
{
    use Queueable, SerializesModels, CanBeLogged;

    public string $body;

    public Collection $fileMeta;

    public ?User $recipient = null;

    private User $fromUser;

    /**
     * Create a new message instance.
     */
    public function __construct(
        UserGroup $group,
        string $subject,
        string $body,
        User $fromUser,
        Collection $fileMeta,
        string $uid,
        int $size,
    ) {
        $this->to($group->email, $group->title);
        $this->subject($subject);
        $this->from($fromUser->email, $fromUser->name);
        $this->body = $body;
        $this->fileMeta = $fileMeta;

        $this->uid = $uid;
        $this->received_at = now();
        $this->has_attachments = $fileMeta->isNotEmpty();
        $this->size = $size;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $this->fileMeta->each(fn ($file) => $this->attachFromStorageDisk('temp', 'broadcasts/'.$file['hashName'], $file['originalName']));

        if ($this->recipient === null) {
            throw new LogicException('A recipient is required to render an organisation broadcast.');
        }

        $emailPreferenceLinks = app(EmailPreferenceLinks::class)->make($this->recipient, EmailCategory::COMMUNICATIONS);

        return $this->markdown('emails.broadcast', [
            'body' => $this->body,
            'emailPreferenceLinks' => $emailPreferenceLinks,
        ]);
    }

    public function forRecipient(User $recipient): void
    {
        $this->recipient = $recipient;
    }

    public function getContent(): string
    {
        return $this->body;
    }
}
