<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TenantWelcomePart1 extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public User $owner)
    {
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        return new Envelope(
            from: new Address(config('mail.sales.address'), config('mail.sales.name')),
	        subject: 'Welcome to Choir Concierge - A Labour of Love',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
			markdown: 'emails.tenant_welcome_1',
	        with: [
				'owner' => $this->owner,
	        ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
