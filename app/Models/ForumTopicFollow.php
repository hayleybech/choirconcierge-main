<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ForumTopicFollow extends Model
{
    use BelongsToTenant;

    protected $fillable = ['membership_id', 'forum_topic_id'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
