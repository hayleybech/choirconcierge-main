<?php


namespace App\Mail;


use App\Models\UserGroup;
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

    public function resendToGroup(): void
    {
        $group = $this->getMatchingGroup();
        if( ! $group )
        {
            return;
        }

        $group_email = $group->slug . '@' . config('imap.host_display');

        $users = $group->get_all_users();
        foreach($users as $user)
        {
            $this->to = [];
            Mail::to( $user )
                ->cc( $group_email )
                ->send( $this );
        }
    }

    public function getMatchingGroup(): ?UserGroup
    {
        $group_slug =  explode( '@', $this->to[0]['address'])[0];

        return UserGroup::where('slug', 'LIKE', $group_slug)->first();
    }
}