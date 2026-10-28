<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ForumReaction extends Model
{
    use BelongsToTenant;

    public const SUPPORTED_EMOJIS = ['👍', '❤️', '😂', '🎉', '👀'];

    protected $fillable = ['membership_id', 'emoji'];

    public function reactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function membership(): BelongsTo
    {
        return $this->belongsTo(Membership::class);
    }
}
