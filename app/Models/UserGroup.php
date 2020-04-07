<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * Class UserGroup
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string $list_type
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property GroupMember[] $members
 *
 * @package App\Models
 */
class UserGroup extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title', 'slug', 'list_type',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    public function roles(): MorphToMany
    {
        return $this->morphedByMany( Role::class, 'memberable', 'group_members', 'group_id');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany( User::class, 'memberable', 'group_members', 'group_id');
    }
}
