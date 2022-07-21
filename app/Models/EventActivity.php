<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Columns
 * @property int $id
 * @property int $event_id
 * @property string $description
 * @property int $duration
 * @property int $order
 * @property int $song_id
 *
 * Relationships
 * @property Event $event
 * @property Song $song
 */

class EventActivity extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function song(): BelongsTo
    {
        return $this->belongsTo(Song::class);
    }
}
