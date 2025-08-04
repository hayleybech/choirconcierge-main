<?php

namespace App\Mail;

use App\Models\MailLog;
use App\Models\User;
use App\Models\UserGroup;
use Exception;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;

class IncomingMessage extends Mailable implements Loggable
{
    use CanBeLogged;

    public $content_html;

    public $content_text;

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build(): self
    {
        return $this->view('emails.clone')->text('emails.clone_plain');
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
        $this->getMatchingGroups()
            ->flatten(1)
            ->filter(fn (UserGroup $group) => $this->authoriseSenderForGroup($group))
            ->each(fn (UserGroup $group) => CloneMessage::forGroup(clone $this, $group));
    }

    public function getMatchingGroups(): Collection
    {
        $recipients_found_by_type = collect([
            'to' => collect($this->to),
            'cc' => collect($this->cc),
            'bcc' => collect($this->bcc),
            'from' => collect($this->from),
        ])->map(
            fn (Collection $recipients) => $recipients
                ->map(fn ($recipient) => $this->getGroupByEmail($recipient['address']))
                ->filter(),
        );

        // Allow reply-all by cloning emails CCd to the group
        // Don't allow cloning the initial email
        $recipients_found_by_type['cc'] = $recipients_found_by_type['cc']->diff($recipients_found_by_type['from']);

        return $recipients_found_by_type->except('from');
    }

    private function getGroupByEmail(string $email): ?UserGroup
    {
        if(!$email || !str_contains($email, '@')) {
            MailLog::firstWhere('uid', $this->uid)->events()->create([
                'status' => 'malformed-recipient',
                'context' => $email,
            ]);
            return null;
        }

        try {
            $group = UserGroup::withoutTenancy()
            ->byEmail($email)
            ->firstOrFail();

            MailLog::firstWhere('uid', $this->uid)->events()->create([
                'status' => 'group-found',
                'context' => $group->title,
                'user_group_id' => $group->id,
            ]);

            return $group;
        }
        catch (Exception) {
            MailLog::firstWhere('uid', $this->uid)->events()->create([
                'status' => 'group-not-found',
                'context' => $email,
            ]);

            return null;
        }
    }

    private function authoriseSenderForGroup(UserGroup $group): bool
    {
        if ($group->authoriseSender(User::firstWhere('email', $this->from[0]['address']))) {
            return true;
        }

        Mail::to($this->from[0]['address'])->send(new NotPermittedSenderMessage($group));

        MailLog::firstWhere('uid', $this->uid)->events()->create([
            'status' => 'rejected-sender',
            'context' => $group->title,
        ]);

        return false;
    }

    public function getContent(): string
    {
        return $this->content_html ?? $this->content_text ?? '';
    }
}
