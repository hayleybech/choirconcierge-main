<?php

namespace App\Models;

use App\Mail\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    use HasFactory;

    protected $guarded = [];

    public function events(): HasMany
    {
        return $this->hasMany(MailLogEvent::class);
    }

    public function latestEvent(): HasOne
    {
        return $this->hasOne(MailLogEvent::class)->latestOfMany();
    }

    public function opens(): HasMany
    {
        return $this->hasMany(MailLogEvent::class)->where('status', 'opened');
    }

    /**
     * Get the body of the email, stripping the tracking pixel to avoid triggering it when viewing in the app.
     */
    public function getBodyAttribute($value): string
    {
        return preg_replace('/<img [^>]*src="[^"]*\/mail-log\/open\/[^"]*"[^>]*>/i', '', $value);
    }

    public function tenants(): BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'mail_log_tenant');
    }

    public static function createFromMessage(Loggable $message) {
        $mailLog = self::create([
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
            'subject' => Str::limit($message->subject, 128-3),
            'body' => $message->getContent(),
            'has_attachments' => $message->getHasAttachments(),
            'received_at' => $message->getReceivedAt(),
        ]);

        $recipients = collect($message->to)
            ->merge($message->cc)
            ->merge($message->bcc)
            ->map(fn($item) => $item['address'])
            ->filter();

        if ($recipients->isNotEmpty()) {
            $domains = $recipients->map(fn($email) => Str::after($email, '@'))->unique();

            $centralDomain = central_domain();
            $tenants = Tenant::all()->filter(function ($tenant) use ($domains, $centralDomain) {
                return $domains->contains(function ($domain) use ($tenant, $centralDomain) {
                    return $domain === $tenant->primary_domain . '.' . $centralDomain;
                });
            });

            if ($tenants->isNotEmpty()) {
                $mailLog->tenants()->syncWithoutDetaching($tenants->pluck('id'));
            }
        }

        return $mailLog;
    }
}
