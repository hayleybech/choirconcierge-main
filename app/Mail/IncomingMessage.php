<?php


namespace App\Mail;


use App\ManuallyInitializeTenancyByDomainOrSubdomain;
use App\Models\User;
use App\Models\UserGroup;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Stancl\Tenancy\Exceptions\TenantCouldNotBeIdentifiedById;

class IncomingMessage extends Mailable
{
    public $content_html;
    public $content_text;
    private $original_sender;

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
    public function resendToGroups(): void
    {
        try {
            app(ManuallyInitializeTenancyByDomainOrSubdomain::class)->handle( explode('@', $this->to[0]['address'])[1] );
        }
        catch (TenantCouldNotBeIdentifiedById $e) {
            return;
        }

        $this->original_sender = $this->from[0];

        // Clear replyTo, then put the original sender as the reply-to
        $this->replyTo = [[
            'address' => $this->original_sender['address'],
            'name' => $this->original_sender['name'] ?? null
        ]];

        $group = $this->getMatchingGroups()->flatten(1)[0];
        if( ! $group )
        {
            return;
        }

        if( ! $this->authoriseSenderForGroup($group) )
        {
            return;
        }

        $group->get_all_recipients()
            ->each(fn($user) => $this->resendToUser($user, $group));
    }

    public function getMatchingGroups(): Collection
    {
    	$recipients_found_by_type = collect([
            'to'    => collect($this->to),
            'cc'    => collect($this->cc),
            'bcc'   => collect($this->bcc),
            'from'  => collect($this->from),
            ]
        )->map(fn(Collection $recipients) => $recipients
            ->map(fn($recipient) => $this->getGroupByEmail($recipient['address']))
            ->filter(fn($recipient) => $recipient)
        );

        // Allow reply-all by cloning emails CCd to the group
        // Don't allow cloning the initial email
        $recipients_found_by_type['cc'] = $recipients_found_by_type['cc']->diff($recipients_found_by_type['from']);

        return $recipients_found_by_type->except('from');
    }

    private function getGroupByEmail(string $email): ?UserGroup
    {
        [$slug, $host] = explode( '@', $email);
        return UserGroup::withoutTenancy()->firstWhere([
            ['tenant_id', '=', explode('.', $host)[0]],
            ['slug', 'LIKE', $slug],
        ]);
    }

    private function authoriseSenderForGroup(UserGroup $group): bool
    {
        if( $group->authoriseSender(User::firstWhere('email', '=', $this->original_sender['address'])) )
        {
            return true;
        }

        Mail::to($this->original_sender['address'])->send(new NotPermittedSenderMessage($group));
        return false;
    }

    private function resendToUser(User $user, UserGroup $group): void
    {
        // Clear from, then put the mailing list as the clone sender
        // e.g. From: Mr Director via Active Members <active@example.choirconcierge.com>
        $this->from = [[
            'address' => $group->email,
            'name' => ($this->original_sender['name'] ?? $this->original_sender['address']) . ' via ' . $group->title
        ]];

        // Clear 'to', then put the group member as the recipient
        $this->to = [];
        Mail::to( $user )
            ->cc( $group->email ) // Required for recipients to reply-all
            ->send( clone $this );

    }
}