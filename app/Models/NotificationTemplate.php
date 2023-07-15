<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use App\Notifications\TaskCompleted;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

/**
 * Class NotificationTemplate
 *
 * Columns
 * @property int $id
 * @property int $task_id
 * @property string $recipients
 * @property string $subject
 * @property string $body
 * @property string $delay
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 *
 * Relationships
 * @property Task $task
 */
class NotificationTemplate extends Model
{
    use SoftDeletes, HasFactory, TenantTimezoneDates;

    protected $fillable = ['subject', 'recipients', 'body', 'delay'];

    protected $with = ['task'];

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class);
    }

    public function getBodyWithHighlightsAttribute()
    {
        $replacements = [
            '%%singer.name%%' => '<code class="text-brand-blue">%%singer.name%%</code>',
            '%%singer.fname%%' => '<code class="text-brand-blue">%%singer.fname%%</code>',
            '%%singer.lname%%' => '<code class="text-brand-blue">%%singer.lname%%</code>',
            '%%singer.email%%' => '<code class="text-brand-blue">%%singer.email%%</code>',
            '%%placement.create%%' => '<code class="text-brand-blue">%%placement.create%%</code>',
            '%%choir.name%%' => '<code class="text-brand-blue">%%choir.name%%</code>',
            '%%organisation.name%%' => '<code class="text-brand-blue">%%organisation.name%%</code>',
            '%%singer.dob%%' => '<code class="text-brand-blue">%%singer.dob%%</code>',
            '%%singer.age%%' => '<code class="text-brand-blue">%%singer.age%%</code>',
            '%%singer.phone%%' => '<code class="text-brand-blue">%%singer.phone%%</code>',
            '%%singer.section%%' => '<code class="text-brand-blue">%%singer.section%%</code>',
            '%%user.name%%' => '<code class="text-brand-blue">%%user.name%%</code>',
            '%%user.fname%%' => '<code class="text-brand-blue">%%user.fname%%</code>',
            '%%user.lname%%' => '<code class="text-brand-blue">%%user.lname%%</code>',
        ];

        return str_replace(array_keys($replacements), $replacements, nl2br(e($this->body)));
    }

    public function getRecipientType(): string
    {
        $recipient_info = explode(':', $this->recipients);

        return $recipient_info[0];
    }

    // @todo replace with a polymorphic relationship
    public function getRecipients(): array
    {
        $recipients = [];
        [$recipient_type, $recipient_id] = explode(':', $this->recipients);
        switch ($recipient_type) {
            case 'role':
                $role = Role::find($recipient_id);
                $recipients = $role->users->all();
                break;
            case 'user':
                $recipients[] = User::find($recipient_id);
                break;
            case 'singer':
                $recipients[] = Singer::find($recipient_id);
                break;
        }

        return $recipients;
    }

    public function generateBody(Singer $singer, User $user = null): string
    {
        $replacements = [
            '%%singer.name%%' => $singer->user->name,
            '%%singer.fname%%' => $singer->user->first_name,
            '%%singer.lname%%' => $singer->user->last_name,
            '%%singer.email%%' => $singer->user->email,
            '%%placement.create%%' => '', //route( 'placement.create', $singer, $this->task ),
            '%%choir.name%%' => tenant('name') ?? 'Organisation Name',
            '%%organisation.name%%' => tenant('name') ?? 'Organisation Name',
        ];
        $profile_replacements = [
            '%%singer.dob%%' => $singer->user->dob,
            '%%singer.age%%' => $singer->getAge(),
            '%%singer.phone%%' => $singer->user->phone,
        ];
        $replacements = array_merge($replacements, $profile_replacements);
        if ($singer->placement) {
            $placement_replacements = [
                '%%singer.section%%' => $singer->placement->voice_part,
            ];
            $replacements = array_merge($replacements, $placement_replacements);
        }
        if ($user) {
            $user_replacements = [
                '%%user.name%%' => $user->name,
                '%%user.fname%%' => $user->first_name,
                '%%user.lname%%' => $user->last_name,
            ];
            $replacements = array_merge($replacements, $user_replacements);
        }

        $body = str_replace(array_keys($replacements), $replacements, $this->body);

        return $body;
    }

    /**
     * @param Singer $singer
     */
    public function generateNotifications(Singer $singer): void
    {
        // Only generate in-app notifications for actual users
        if ($this->getRecipientType() === 'role' || $this->getRecipientType() === 'user') {
            $recipients = $this->getRecipients();
        } else {
            $recipients[] = $singer;
        }

        // Loop through recipients for this template to create Notifications
        foreach ($recipients as $recipient) {
            if ($this->getRecipientType() === 'role' || $this->getRecipientType() === 'user') {
                $body = $this->generateBody($singer, $recipient);
            } else {
                $body = $this->generateBody($singer);
            }

            $when = Carbon::now()->add(\DateInterval::createFromDateString($this->delay));
            $recipient->notify(
                (new TaskCompleted($this->subject, $body))
                    ->onConnection('database')
                    ->onQueue('default')
                    ->delay($when),
            );
        }
    }
}
