<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
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
    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'document_upload',
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    public function delete()
    {
        if( Storage::disk('public')->exists( $this->getPath() ) ) {
            Storage::disk('public')->delete( $this->getPath() );
        }

        parent::delete();
    }

    public function setDocumentUploadAttribute(UploadedFile $file): void
    {
        if( Storage::disk('public')->putFile(self::getDownloadsPath(), $file) )
        {
            $this->title = $file->getClientOriginalName();
            $this->filepath = $file->hashName();
        }
    }


    public function getDownloadUrlAttribute(): string
    {
        return Storage::url( $this->getPath() );
    }

    public function getPath(): string
    {
        return self::getDownloadsPath() . '/'. $this->filepath;
    }

    public static function getDownloadsPath(): string
    {
        return 'documents';
    }

}
