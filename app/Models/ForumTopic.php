<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ForumTopic extends Model
{
    /** @use HasFactory<\Database\Factories\ForumTopicFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $fillable = [
        'forum_category_id',
        'membership_id',
        'title',
        'body',
        'is_locked',
        'is_pinned',
    ];

    protected $appends = ['body_html'];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'is_pinned' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'forum_category_id');
    }

    public function authorMembership(): BelongsTo
    {
        return $this->belongsTo(Membership::class, 'membership_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class);
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
