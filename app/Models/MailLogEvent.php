<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class MailLogEvent
 *
 * Columns
 * @property int $id
 * @property string $status Status within Concierge's system
 * @property string $context Extra details e.g. the email that was rejected
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property int $mail_log_id
 * @property int $user_group_id
 *
 * Relationships
 * @property MailLog $ail_log
 * @property UserGroup $user_group
 */
class MailLogEvent extends Model
{
    protected $guarded = [];

    public function mail_log(): BelongsTo
    {
        return $this->belongsTo(MailLog::class);
    }

    public function user_group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
