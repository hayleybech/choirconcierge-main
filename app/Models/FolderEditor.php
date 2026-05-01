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
    protected $fillable = ['folder_id', 'editor_id', 'editor_type'];

    /**
     * Get all of the editor models (users, roles etc).
     */
    public function editor(): MorphTo
    {
        if (in_array($this->editor_type, ['App\Enums\SingerStatus', \App\Enums\SingerStatus::class, 'SingerStatus'])) {
            return $this->morphTo('editor', User::class, 'editor_id', 'id')->whereRaw('1 = 0');
        }

        return $this->morphTo();
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }
}
