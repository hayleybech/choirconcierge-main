<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ForumCategory extends Model
{
    /** @use HasFactory<\Database\Factories\ForumCategoryFactory> */
    use BelongsToTenant, HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $attributes = [
        'is_choir_wide' => true,
    ];

    protected $casts = [
        'is_choir_wide' => 'boolean',
    ];

    public function ensembles(): BelongsToMany
    {
        return $this->belongsToMany(Ensemble::class, 'ensemble_forum_category');
    }

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class);
    }
}
