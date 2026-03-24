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
    protected $fillable = ['viewer_id', 'viewer_type'];

    /**
     * Get all of the viewer models (users, roles etc).
     */
    public function viewer(): MorphTo
    {
        return $this->morphTo();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
