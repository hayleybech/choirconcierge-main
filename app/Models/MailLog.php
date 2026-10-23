<?php

namespace App\Models;

use App\Mail\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
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
 * @property int $size Size in bytes
 * @property boolean $has_attachments
 * @property Carbon $received_at The date the email arrived in the mailbox
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class MailLog extends Model
{
    use HasFactory;

    public const TYPE_LABELS = [
        'broadcast' => 'Broadcast',
        'inbound' => 'Inbound',
        'notification' => 'Notification',
        'product' => 'Product Announcement',
    ];

    public const DEFAULT_TYPES = ['broadcast', 'inbound', 'product'];

    protected $guarded = [];

    public function isProductAnnouncement(): bool
    {
        return str_starts_with($this->uid, 'product-announcement-');
    }

    /**
     * @param array<int, string> $types
     */
    public static function applyTypeFilter(Builder $query, array $types): Builder
    {
        $types = array_values(array_intersect($types, array_keys(self::TYPE_LABELS)));

        if ($types === []) {
            return $query->whereRaw('1 = 0');
        }

        return $query->where(function (Builder $query) use ($types): void {
            foreach ($types as $type) {
                $prefix = $type === 'product' ? 'product-announcement' : $type;
                $query->orWhere('uid', 'like', $prefix . '-%');
            }
        });
    }

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
            'from' => Str::limit(self::formatRecipients($message->from), 254),
            'to' => Str::limit(self::formatRecipients($message->to), 512),
            'cc' => Str::limit(self::formatRecipients($message->cc), 254),
            'bcc' => Str::limit(self::formatRecipients($message->bcc), 254),
            'subject' => Str::limit($message->subject, 128-3),
            'body' => $message->getContent(),
            'size' => $message->getSize(),
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

    private static function formatRecipients(array $recipients): string
    {
        return collect($recipients)
            ->map(function (array $recipient): string {
                $address = $recipient['address'];
                $name = $recipient['name'] ?? null;

                return filled($name) ? $name.' <'.$address.'>' : $address;
            })
            ->join(', ');
    }
}
