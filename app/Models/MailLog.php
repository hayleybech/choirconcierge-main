<?php

namespace App\Models;

use App\Mail\Loggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Class MailLog
 *
 * Columns
 * @property int $id
 * @property string $uid Only guaranteed to be unique within the same mailbox
 * @property string $from Max email length according to intl standard
 * @property string $to Max length is arbitrary, assumes several regular-length recipients
 * @property string $cc Max email length according to intl standard
 * @property string $bcc Max email length according to intl standard
 * @property string $subject Max length is arbitrary
 * @property string $body Max length is arbitrary, but matches the max length for sending broadcasts
 * @property boolean $has_attachments
 * @property Carbon $received_at The date the email arrived in the mailbox
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MailLog extends Model
{
    protected $guarded = [];

    public function events(): HasMany
    {
        return $this->hasMany(MailLogEvent::class);
    }

    public function latestEvent(): HasOne
    {
        return $this->hasOne(MailLogEvent::class)->latestOfMany();
    }

    public static function createFromMessage(Loggable $message) {
        return self::create([
            'uid' => $message->getUid(),
            'from' => collect($message->from)
                ->map(fn($item) => $item['address'])
                ->join(', '),
            'to' => collect($message->to)
                ->map(fn($item) => $item['address'])
                ->join(', '),
            'cc' => collect($message->cc)
                ->map(fn($item) => $item['address'])
                ->join(', '),
            'bcc' => collect($message->bcc)
                ->map(fn($item) => $item['address'])
                ->join(', '),
            'subject' => Str::limit($message->subject, 128),
            'body' => Str::limit($message->getContent(), 5000),
            'has_attachments' => $message->getHasAttachments(),
            'received_at' => $message->getReceivedAt(),
        ]);
    }
}
