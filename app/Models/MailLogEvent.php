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
 */
class MailLogEvent extends Model
{
    protected $guarded = [];

    public function mailLog(): BelongsTo
    {
        return $this->belongsTo(MailLog::class);
    }
}
