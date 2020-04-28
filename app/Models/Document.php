<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Storage;

/**
 * Class Document
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property string $filepath
 * @property int $folder_id
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Document extends Model
{
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function getDownloadUrlAttribute(): string
    {
        return Storage::url( self::getDownloadsPath() . '/'. $this->filepath);
    }

    public static function getDownloadsPath(): string
    {
        return 'documents';
    }

}
