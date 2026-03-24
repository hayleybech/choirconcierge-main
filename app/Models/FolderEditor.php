<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class FolderEditor extends Model
{
    use TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['editor_id', 'editor_type'];

    /**
     * Get all of the editor models (users, roles etc).
     */
    public function editor(): MorphTo
    {
        return $this->morphTo();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
