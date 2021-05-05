<?php


namespace App\Mail;


use App\ManuallyInitializeTenancyByDomainOrSubdomain;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Mail\Mailable;
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

        $group = $this->getMatchingGroup();
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

    public function getMatchingGroup(): ?UserGroup
    {
    	// @todo make DTOs
    	$recipients_raw_by_type = [
    		'to'    => $this->to,
		    'cc'    => $this->cc,
		    'from'  => $this->from,
	    ];
    	$recipients_found_by_type = [
    		'to'    => [],
		    'cc'    => [],
		    'from'  => [],
	    ];

    	// @todo test for multiple tenants
    	foreach($recipients_raw_by_type as $recipient_type => $recipients_raw){
    		foreach($recipients_raw as $recipient_raw){
			    $slug =  explode( '@', $recipient_raw['address'])[0];
			    $group = UserGroup::firstWhere('slug', 'LIKE', $slug);

			    if(!$group) {
			    	continue;
			    }
			    $recipients_found_by_type[$recipient_type][] = $group;
		    }
	    }

	    // Allow reply-all by cloning emails CCd to the group
	    // Don't allow cloning the initial email
    	$recipients_found_by_type['cc'] = array_diff($recipients_found_by_type['cc'], $recipients_found_by_type['from']);

    	// temporary code. return only the FIRST result found.
	    // @todo return multiple matches
    	return $recipients_found_by_type['to'][0] ??
		    $recipients_found_by_type['cc'][0] ?? null;
		    //$recipients_found_by_type['from'][0] ?? null;
    }
}