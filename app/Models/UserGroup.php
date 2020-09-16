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
 * @property Role[] $recipient_roles
 * @property User[] $recipient_users
 * @property VoicePart[] $recipient_voice_parts
 * @property SingerCategory[] $recipient_singer_categories
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
        $group->syncPolymorhpic( $attributes['recipient_roles'] ?? [], Role::class, GroupMember::class, 'members', 'memberable', 'group_id' );
        $group->syncPolymorhpic( $attributes['recipient_voice_parts'] ?? [], VoicePart::class, GroupMember::class, 'members', 'memberable', 'group_id' );
        $group->syncPolymorhpic( $attributes['recipient_users'] ?? [], User::class, GroupMember::class, 'members', 'memberable', 'group_id' );
        $group->syncPolymorhpic( $attributes['recipient_singer_categories'] ?? [], SingerCategory::class, GroupMember::class, 'members', 'memberable', 'group_id' );

        $group->save();

        return $group;
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Update recipients
        $this->syncPolymorhpic( $attributes['recipient_roles'] ?? [], Role::class, GroupMember::class, 'members', 'memberable', 'group_id' );
        $this->syncPolymorhpic( $attributes['recipient_voice_parts'] ?? [], VoicePart::class, GroupMember::class, 'members', 'memberable', 'group_id' );
        $this->syncPolymorhpic( $attributes['recipient_users'] ?? [], User::class, GroupMember::class, 'members', 'memberable', 'group_id' );
        $this->syncPolymorhpic( $attributes['recipient_singer_categories'] ?? [], SingerCategory::class, GroupMember::class, 'members', 'memberable', 'group_id' );
        $this->save();
    }
    public function members(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id');
    }

    public function recipient_roles(): MorphToMany
    {
        return $this->morphedByMany( Role::class, 'memberable', 'group_members', 'group_id');
    }

    public function recipient_voice_parts(): MorphToMany
    {
        return $this->morphedByMany( VoicePart::class, 'memberable', 'group_members', 'group_id');
    }

    public function recipient_users(): MorphToMany
    {
        return $this->morphedByMany( User::class, 'memberable', 'group_members', 'group_id');
    }

    public function recipient_singer_categories(): MorphToMany
    {
        return $this->morphedByMany( SingerCategory::class, 'memberable', 'group_members', 'group_id');
    }

    public function get_all_recipients()
    {
        /* @todo use queries instead */

        $cat_ids = $this->recipient_singer_categories()->get()->pluck('id');

        // Get directly-assigned users
        $users = $this->recipient_users()
            ->whereHas('singer', function($singer_query) use($cat_ids) {
                $singer_query->whereIn('singer_category_id', $cat_ids );
            })
            ->get();

        foreach( $this->recipient_roles as $role )
        {
            $role_users = $role->users()
                ->whereHas('singer', function($singer_query) use($cat_ids) {
                    $singer_query->whereIn('singer_category_id', $cat_ids );
                })
                ->get();
            $users = $users->merge( $role_users );
        }

        // Get users from voice parts
        $voice_part_ids = $this->recipient_voice_parts()->get()->pluck('id')->toArray();
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
     * @param int[]  $poly_ids The ids to sync
     * @param string $poly_type The type we're currently syncing
     * @param string $poly_class The class name of the polymorphic model
     * @param string $poly_relationship The name of the other model's relationship to the polymorph
     * @param string $poly_name The name of the polymorph used in table columns (x_id, x_type)
     * @param string $related_id_col The name of the foreign key column connecting the polymorph to the other model
     */
    public function syncPolymorhpic(array $poly_ids, string $poly_type, string $poly_class, string $poly_relationship, string $poly_name, string $related_id_col ): void
    {
        // Detach the records not listed in the incoming array
        $poly_class::where($related_id_col, '=', $this->id)
                ->where($poly_name.'_type', '=', $poly_type)
                ->whereNotIn($poly_name.'_id', $poly_ids)
                ->delete();

        // Insert new records
        $unchanged_ids = $poly_class::where($related_id_col, '=', $this->id)
            ->where($poly_name.'_type', '=', $poly_type)
            ->whereIn($poly_name.'_id', $poly_ids)
            ->pluck($poly_name.'_id')
            ->toArray();
        $new_poly_ids = array_diff( $poly_ids, $unchanged_ids );

        $attach = [];
        foreach($new_poly_ids as $new_poly_id) {
            $attach[] = [
                $poly_name.'_id' => $new_poly_id,
                $poly_name.'_type' => $poly_type,
            ];
        }
        $this->fresh()->$poly_relationship()->createmany($attach);
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
            return $this->get_all_recipients()->contains($user);
        }
        return false;
    }
}
