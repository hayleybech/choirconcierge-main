<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Columns
 * @property int $event_id
 * @property int $order
 * @property int $activity_type_id
 * @property int $song_id
 * @property int $singer_id
 * @property string $notes
 * @property int $duration
 *
 * Relationships
 * @property Event $event
 * @property ActivityType $activityType
 * @property Song $song
 * @property Singer $assignee
 */
class EventActivity extends Model
{
    use HasFactory;

    protected $guarded = [];
}
