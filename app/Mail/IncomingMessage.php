<?php


namespace App\Mail;


use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Mailable;
use Mail;

class IncomingMessage extends Mailable
{
    public $content_html;
    public $content_text;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.clone')
            ->text('emails.clone_plain');
    }

    /**
     * Generate clones of the email
     *
     * EXAMPLE
     *  From: Mr Director via Active Members <active@example.choirconcierge.com>
     *  Reply-to: Mr Director <director@example.com>
     *  To: Ms User <user@example.com>
     *  Cc: Active Members <active@example.choirconcierge.com>
     */
    public function resendToGroup(): void
    {
        $group = $this->getMatchingGroup();
        if( ! $group )
        {
            return;
        }

        $group_email = $group->slug . '@' . config('imap.host_display');
        $original_sender = $this->from[0];

        $users = $group->get_all_users();
        foreach($users as $user)
        {
            // Clear replyTo, then put the original sender as the reply-to
            $this->replyTo = [[
                'address' => $original_sender['address'],
                'name' => $original_sender['name'] ?? null
            ]];

            // Clear from, then put the mailing list as the clone sender
            // e.g. From: Mr Director via Active Members <active@example.choirconcierge.com>
            $this->from = [[
                'address' => $group_email,
                'name' => ($original_sender['name'] ?? $original_sender['address']) . ' via ' . $group->title
            ]];

            // Clear 'to', then put the group member as the recipient
            $this->to = [];
            Mail::to( $user )
                ->cc( $group_email ) // Required for recipients to reply-all
                ->send( $this );
        }
    }

    public function getMatchingGroup(): ?UserGroup
    {
        $to_slug = explode( '@', $this->to[0]['address'])[0];
        $cc_slug = explode( '@', $this->cc[0]['address'] ?? '')[0] ?? '';
        $from_slug = explode( '@', $this->from[0]['address'])[0];

        $query = UserGroup::where('slug', 'LIKE', $to_slug);

        // Allow reply-all by cloning emails CCd to the group
        if($from_slug !== $cc_slug){ // Don't allow cloning the initial email
            $query = $query->orWhere('slug', 'LIKE', $cc_slug);
        }
        return $query->first();
    }
}