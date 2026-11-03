<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ForumTopicRead extends Model
{
    use BelongsToTenant;

    protected $fillable = ['membership_id', 'last_read_reply_id', 'read_at'];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }

    public function lastReadReply(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'last_read_reply_id');
    }
}
