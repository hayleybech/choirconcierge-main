<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
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

    public function getFileIcon(): string
    {
        $extn_icons = [
            // text
            'txt'   => 'fa-file-alt',
            'md'    => 'fa-file-alt',
            'rtf'   => 'fa-file-alt',
            // word
            'doc'   => 'fa-file-word',
            'docm'  => 'fa-file-word',
            'docx'  => 'fa-file-word',
            'dot'   => 'fa-file-word',
            'dotm'  => 'fa-file-word',
            'dotx'  => 'fa-file-word',
            'odt'   => 'fa-file-word',
            'ott'   => 'fa-file-word',
            // powerpoint
            'odp'   => 'fa-file-powerpoint',
            'otp'   => 'fa-file-powerpoint',
            'pot'   => 'fa-file-powerpoint',
            'potm'   => 'fa-file-powerpoint',
            'potx'   => 'fa-file-powerpoint',
            'pps'   => 'fa-file-powerpoint',
            'ppsm'   => 'fa-file-powerpoint',
            'ppsx'   => 'fa-file-powerpoint',
            'ppt'   => 'fa-file-powerpoint',
            'pptm'   => 'fa-file-powerpoint',
            'pptx'   => 'fa-file-powerpoint',
            // pdf
            'pdf'   => 'fa-file-pdf',
            // video
            'mpg'   => 'fa-file-video',
            'mp2'   => 'fa-file-video',
            'mpeg'  => 'fa-file-video',
            'ogg'   => 'fa-file-video',
            'avi'   => 'fa-file-video',
            'wmv'   => 'fa-file-video',
            'mov'   => 'fa-file-video',
            'qt'    => 'fa-file-video',
            'flv'   => 'fa-file-video',
            'swf'   => 'fa-file-video',
            'webm'  => 'fa-file-video',
            // audio
            '3gp'   => 'fa-file-audio',
            'mp3'   => 'fa-file-audio',
            'mp4'   => 'fa-file-audio',
            'm4p'   => 'fa-file-audio',
            'm4v'   => 'fa-file-audio',
            'wav'   => 'fa-file-audio',
            'wma'   => 'fa-file-audio',
            // csv
            'csv'   => 'fa-file-csv',
            'tsv'   => 'fa-file-csv',
            // spreadsheet
            'xlm'   => 'fa-file-spreadsheet',
            'ods'   => 'fa-file-spreadsheet',
            'ots'   => 'fa-file-spreadsheet',
            'xls'   => 'fa-file-spreadsheet',
            'xlsb'  => 'fa-file-spreadsheet',
            'xlsm'  => 'fa-file-spreadsheet',
            'xlsx'  => 'fa-file-spreadsheet',
            'xlt'   => 'fa-file-spreadsheet',
            'xltm'  => 'fa-file-spreadsheet',
            'xltx'  => 'fa-file-spreadsheet',
            // image
            'bmp'   => 'fa-file-image',
            'gif'   => 'fa-file-image',
            'ico'   => 'fa-file-image',
            'jpg'   => 'fa-file-image',
            'jpeg'  => 'fa-file-image',
            'png'   => 'fa-file-image',
            'psd'   => 'fa-file-image',
            'tiff'  => 'fa-file-image',
            'webp'  => 'fa-file-image',
            'eps'   => 'fa-file-image',
            'svg'   => 'fa-file-image',
        ];
        $default_icon = 'fa-file';

        $extension = Str::afterLast($this->title, '.');

        return $extn_icons[$extension] ?? $default_icon;
    }

}
