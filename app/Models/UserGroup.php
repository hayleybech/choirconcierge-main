<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

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
 * @property SingerCategory[] $singer_categories
 *
 * @package App\Models
 */
class UserGroup extends Model
{
    use BelongsToTenant, SoftDeletes;

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
        $group->syncPolymorhpic( $attributes['recipient_singer_categories'] ?? [], SingerCategory::class );
        $group->save();

        return $group;
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Update recipients
        $this->syncPolymorhpic( $attributes['recipient_roles'] ?? [], Role::class );
        $this->syncPolymorhpic( $attributes['recipient_voice_parts'] ?? [], VoicePart::class );
        $this->syncPolymorhpic( $attributes['recipient_users'] ?? [], User::class );
        $this->syncPolymorhpic( $attributes['recipient_singer_categories'] ?? [], SingerCategory::class );
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

    public function singer_categories(): MorphToMany
    {
        return $this->morphedByMany( SingerCategory::class, 'memberable', 'group_members', 'group_id');
    }

    public function get_all_users()
    {
        /* @todo use queries instead */

        $cat_ids = $this->singer_categories()->get()->pluck('id');

        // Get directly-assigned users
        $users = $this->users()
            ->whereHas('singer', function($singer_query) use($cat_ids) {
                $singer_query->whereIn('singer_category_id', $cat_ids );
            })
            ->get();

        foreach( $this->roles as $role )
        {
            $role_users = $role->users()
                ->whereHas('singer', function($singer_query) use($cat_ids) {
                    $singer_query->whereIn('singer_category_id', $cat_ids );
                })
                ->get();
            $users = $users->merge( $role_users );
        }

        // Get users from voice parts
        $voice_part_ids = $this->voice_parts()->get()->pluck('id')->toArray();
        $part_users = User::query()
            ->whereHas('singer', function ($singer_query) use($voice_part_ids, $cat_ids) {
                $singer_query->whereIn('voice_part_id', $voice_part_ids);
                $singer_query->whereIn('singer_category_id', $cat_ids);
            })
            ->get();
        $users = $users->merge($part_users);
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

    public function authoriseSender(String $senderEmail): bool
    {
        // todo: implement strategy pattern

        if($this->list_type === 'public') {
            return true;
        }

        $user = User::where('email', '=', $senderEmail)->first();
        if( ! $user ){
            return false;
        }

        if($this->list_type === 'chat')
        {
            // check if sender is in recipients list
            return $this->get_all_users()->contains($user);
        }
        return false;
    }
}
