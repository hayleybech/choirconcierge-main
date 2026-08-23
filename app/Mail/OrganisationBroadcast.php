<?php

namespace App\Mail;

use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OrganisationBroadcast extends Mailable implements Loggable
{
    use Queueable, SerializesModels, CanBeLogged;

    public string $body;

    public Collection $fileMeta;

    private User $fromUser;

    /**
     * Create a new message instance.
     */
    public function __construct(UserGroup $group, string $subject, string $body, User $fromUser, Collection $fileMeta, string $uid, int $size)
    {
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

        return $this->markdown('emails.broadcast', ['body' => $this->body]);
    }

    public function getContent(): string
    {
        return $this->body;
    }
}
