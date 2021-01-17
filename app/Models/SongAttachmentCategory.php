<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Webklex\IMAP\Attachment;

/**
 * Class SongAttachmentCategory
 *
 * Columns
 * @property int $id
 * @property string $title
 *
 * Relationships
 * @property Attachment[] $attachments
 *
 * @package App\Models
 */
class SongAttachmentCategory extends Model
{
    use BelongsToTenant, SoftDeletes;

    public $timestamps = false;

    public function attachments(): HasMany
    {
        return $this->hasMany(SongAttachment::class, 'category_id');
    }
}
