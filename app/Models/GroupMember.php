<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class GroupMember extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'memberable_id',
        'memberable_type',
    ];

    /**
     * Get all of the member models (users, roles etc).
     */
    public function memberable(): MorphTo
    {
        return $this->morphTo();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(UserGroup::class);
    }
}
