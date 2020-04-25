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
 * @property Role[] $roles
 * @property User[] $users
 * @property VoicePart[] $voice_parts
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
        $group->syncPolymorhpic( $attributes['recipient_voice_parts'] ?? [], VoicePart::class );
        $group->syncPolymorhpic( $attributes['recipient_users'] ?? [], User::class );
        $group->save();
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Update recipients
        $this->syncPolymorhpic( $attributes['recipient_roles'] ?? [], Role::class );
        $this->syncPolymorhpic( $attributes['recipient_voice_parts'] ?? [], VoicePart::class );
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

    public function voice_parts(): MorphToMany
    {
        return $this->morphedByMany( VoicePart::class, 'memberable', 'group_members', 'group_id');
    }

    public function users(): MorphToMany
    {
        return $this->morphedByMany( User::class, 'memberable', 'group_members', 'group_id');
    }

    public function get_all_users()
    {
        /* @todo use queries instead */

        // Get directly-assigned users
        $users = $this->users;

        // Get users from roles
        foreach( $this->roles as $role )
        {
            $users = $users->merge( $role->users );
        }

        // Get users from voice parts
        foreach( $this->voice_parts as $part )
        {
            $users = $users->merge( $part->users );
        }

        return $users->unique();
    }

    /**
     * @param int[] $memberable_ids
     * @param string $memberable_type
     */
    public function syncPolymorhpic($memberable_ids, string $memberable_type ): void
    {
        // Detach the Memberables not listed in the incoming array
        GroupMember::where('group_id', '=', $this->id)
                ->where('memberable_type', '=', $memberable_type)
                ->whereNotIn('memberable_id', $memberable_ids)
                ->delete();

        // Insert new Memberables
        $unchanged_ids = GroupMember::where('group_id', '=', $this->id)
            ->where('memberable_type', '=', $memberable_type)
            ->whereIn('memberable_id', $memberable_ids)
            ->pluck('memberable_id')
            ->toArray();
        $new = array_diff( $memberable_ids, $unchanged_ids );

        $attach = [];
        foreach($new as $memberable_id) {
            $attach[] = [
                'memberable_id' => $memberable_id,
                'memberable_type' => $memberable_type,
            ];
        }
        $this->fresh()->members()->createmany($attach);
    }
}
