<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class OrganisationBroadcast extends Mailable
{
    use Queueable, SerializesModels;

    public string $body;

    public Collection $fileMeta;

    private User $fromUser;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, string $body, User $fromUser, Collection $fileMeta)
    {
        $this->subject($subject);
        $this->from($fromUser->email, $fromUser->name);
        $this->body = $body;
        $this->fileMeta = $fileMeta;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $this->fileMeta->each(fn ($file) => $this->attachFromStorageDisk('temp', 'broadcasts/'.$file['hashName'], $file['originalName']));

        return $this->markdown('emails.broadcast', ['body' => $this->body]);

    }
}
