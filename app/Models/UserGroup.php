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

    public static function create( array $attributes = [] )
    {
        /** @var UserGroup $group */
        $group = static::query()->create($attributes);

        // Update recipients
        $group->syncPolymorhpic( $attributes['recipient_roles'] ?? [], Role::class );
        $group->syncPolymorhpic( $attributes['recipient_users'] ?? [], User::class );
        $group->save();
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Update recipients
        $this->syncPolymorhpic( $attributes['recipient_roles'] ?? [], Role::class );
        $this->syncPolymorhpic( $attributes['recipient_users'] ?? [], User::class );
        $this->save();
    }
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

    /**
     * @param int[] $memberables
     * @param string $memberable_type
     */
    public function syncPolymorhpic($memberables, string $memberable_type ): void
    {
        // Detach the Memberables not listed in the incoming array
        GroupMember::where('group_id', '=', $this->id)
                ->where('memberable_type', '=', $memberable_type)
                ->whereNotIn('memberable_id', $memberables)
                ->delete();

        // Insert new Memberables
        $unchanged = GroupMember::where('group_id', '=', $this->id)
            ->where('memberable_type', '=', $memberable_type)
            ->whereIn('memberable_id', $memberables)
            ->get()
            ->all();
        $new = array_diff( $memberables, $unchanged );

        $attach = [];
        foreach($new as $memberable_id) {
            $attach[] = [
                'memberable_id' => $memberable_id,
                'memberable_type' => $memberable_type,
            ];
        }
        $this->members()->createMany($attach);
    }
}
