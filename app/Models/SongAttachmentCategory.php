<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class SongAttachmentCategory
 *
 * Columns
 * @property int $id
 * @property string $title
 *
 * Relationships
 * @property Collection<SongAttachment> $attachments
 */
class SongAttachmentCategory extends Model
{
    use BelongsToTenant, SoftDeletes;

    public $timestamps = false;

    public $appends = ['slug'];

    public function slug(): Attribute
    {
        return Attribute::make(
            get: fn (mixed $value, array $attributes) => Str::slug($attributes['title']),
        );
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(SongAttachment::class, 'category_id');
    }
}
