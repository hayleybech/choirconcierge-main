<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
    public function attachments(): HasMany
    {
        return $this->hasMany(SongAttachment::class, 'category_id');
    }
}
