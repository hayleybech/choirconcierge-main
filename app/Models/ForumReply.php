<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ForumReply extends Model
{
    /** @use HasFactory<\Database\Factories\ForumReplyFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'forum_topic_id',
        'parent_id',
        'membership_id',
        'body',
    ];

    protected $appends = ['body_html'];

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'forum_topic_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function authorMembership(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function reactions(): MorphMany
    {
        return $this->morphMany(ForumReaction::class, 'reactionable');
    }

    public function getBodyHtmlAttribute(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
            'max_nesting_level' => 100,
            'max_delimiters_per_line' => 500,
        ]);
    }
}
