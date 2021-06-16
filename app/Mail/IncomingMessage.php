<?php


namespace App\Mail;


use App\ManuallyInitializeTenancyByDomainOrSubdomain;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Mail;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

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
        try {
            app(ManuallyInitializeTenancyByDomainOrSubdomain::class)->handle( explode('@', $this->to[0]['address'])[1] );
        }
        catch (TenantCouldNotBeIdentifiedById $e) {
            return;
        }

        $group = $this->getMatchingGroups()->flatten(1)[0];
        if( ! $group )
        {
            return;
        }

        $original_sender = $this->from[0];
        if( ! $group->authoriseSender($original_sender['address']) )
        {
            Mail::to($original_sender['address'])->send(new NotPermittedSenderMessage($group));

            return;
        }

        $group_email = $this->to[0]['address'];

        $users = $group->get_all_recipients();
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

    public function getMatchingGroups(): Collection
    {
    	$recipients_raw_by_type = collect([
    		'to'    => collect($this->to),
		    'cc'    => collect($this->cc),
		    'bcc'   => collect($this->bcc),
		    'from'  => collect($this->from),
	    ]);

        // @todo test for multiple tenants
    	$recipients_found_by_type = $recipients_raw_by_type
            ->map(fn(Collection $recipients) =>
                $recipients->map(function($recipient) {
                    $slug =  explode( '@', $recipient['address'])[0];
                    return UserGroup::firstWhere('slug', 'LIKE', $slug);
                })
                ->filter(fn($recipient) => $recipient)
            );

        // Allow reply-all by cloning emails CCd to the group
        // Don't allow cloning the initial email
        $recipients_found_by_type['cc'] = $recipients_found_by_type['cc']->diff($recipients_found_by_type['from']);

        return $recipients_found_by_type->except('from');
    }
}