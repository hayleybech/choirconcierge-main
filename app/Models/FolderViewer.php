<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FolderViewer extends Model
{
    use TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['folder_id', 'viewer_id', 'viewer_type'];

    /**
     * Get all of the viewer models (users, roles etc).
     */
    public function viewer(): MorphTo
    {
        if (in_array($this->viewer_type, ['App\Enums\SingerStatus', \App\Enums\SingerStatus::class, 'SingerStatus'])) {
            return $this->morphTo('viewer', User::class, 'viewer_id', 'id')->whereRaw('1 = 0');
        }

        return $this->morphTo();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
