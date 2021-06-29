<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Collection;
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
 * @property Carbon $deleted_at
 * @property int $tenant_id
 *
 * Relationships
 * @property Collection<GroupMember> $members
 * @property Collection<Role> $recipient_roles
 * @property Collection<User> $recipient_users
 * @property Collection<VoicePart> $recipient_voice_parts
 * @property Collection<SingerCategory> $recipient_singer_categories
 *
 * @property Collection<GroupSender> $senders
 * @property Collection<Role> $sender_roles
 * @property Collection<User> $sender_users
 * @property Collection<VoicePart> $sender_voice_parts
 * @property Collection<SingerCategory> $sender_singer_categories
 *
 * Attributes
 * @property string $email
 *
 * @package App\Models
 */
class UserGroup extends Model
{
	use BelongsToTenant, SoftDeletes, HasFactory, TenantTimezoneDates;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['title', 'slug', 'list_type'];

	public static function create(array $attributes = [])
	{
		/** @var UserGroup $group */
		$group = static::query()->create($attributes);

		// Update recipients
		$group->syncPolymorphicMany(GroupMember::class, 'members', 'memberable', 'group_id', [
			Role::class => $attributes['recipient_roles'] ?? [],
			VoicePart::class => $attributes['recipient_voice_parts'] ?? [],
			User::class => $attributes['recipient_users'] ?? [],
			SingerCategory::class => $attributes['recipient_singer_categories'] ?? [],
		]);

		// Update senders
		$group->syncPolymorphicMany(GroupSender::class, 'senders', 'sender', 'group_id', [
			Role::class => $attributes['sender_roles'] ?? [],
			VoicePart::class => $attributes['sender_voice_parts'] ?? [],
			User::class => $attributes['sender_users'] ?? [],
			SingerCategory::class => $attributes['sender_singer_categories'] ?? [],
		]);

		$group->save();

		return $group;
	}

	public function update(array $attributes = [], array $options = [])
	{
		parent::update($attributes, $options);

		// Update recipients
		$this->syncPolymorphicMany(GroupMember::class, 'members', 'memberable', 'group_id', [
			Role::class => $attributes['recipient_roles'] ?? [],
			VoicePart::class => $attributes['recipient_voice_parts'] ?? [],
			User::class => $attributes['recipient_users'] ?? [],
			SingerCategory::class => $attributes['recipient_singer_categories'] ?? [],
		]);

		// Update senders
		$this->syncPolymorphicMany(GroupSender::class, 'senders', 'sender', 'group_id', [
			Role::class => $attributes['sender_roles'] ?? [],
			VoicePart::class => $attributes['sender_voice_parts'] ?? [],
			User::class => $attributes['sender_users'] ?? [],
			SingerCategory::class => $attributes['sender_singer_categories'] ?? [],
		]);

		$this->save();

		return true;
	}
	public function members(): HasMany
	{
		return $this->hasMany(GroupMember::class, 'group_id');
	}

	public function recipient_roles(): MorphToMany
	{
		return $this->morphedByMany(Role::class, 'memberable', 'group_members', 'group_id');
	}

	public function recipient_voice_parts(): MorphToMany
	{
		return $this->morphedByMany(VoicePart::class, 'memberable', 'group_members', 'group_id');
	}

	public function recipient_users(): MorphToMany
	{
		return $this->morphedByMany(User::class, 'memberable', 'group_members', 'group_id');
	}

	public function recipient_singer_categories(): MorphToMany
	{
		return $this->morphedByMany(SingerCategory::class, 'memberable', 'group_members', 'group_id');
	}

	/**
	 * @return Collection<User>
	 */
	public function get_all_recipients(): Collection
	{
		/* @todo use queries instead */

		// Get directly-assigned users
		$users = $this->recipient_users()->get();
		foreach ($this->recipient_roles as $role) {
			$role_users = $role->users()->get();
			$users = $users->merge($role_users);
		}

		// Get users from voice parts
		$voice_part_ids = $this->recipient_voice_parts()
			->get()
			->pluck('id')
			->toArray();
		$part_users = User::query()
			->whereHas('singer', function ($singer_query) use ($voice_part_ids) {
				$singer_query->whereIn('voice_part_id', $voice_part_ids);
			})
			->get();
		$users = $users->merge($part_users);

		// Get singers from categories
		$cat_ids = $this->recipient_singer_categories()
			->get()
			->pluck('id');
		$category_users = User::query()
			->whereHas('singer', function ($singer_query) use ($cat_ids) {
				$singer_query->whereIn('singer_category_id', $cat_ids);
			})
			->get();
		$users = $users->merge($category_users);
		return $users->unique();
	}

	public function senders(): HasMany
	{
		return $this->hasMany(GroupSender::class, 'group_id');
	}

	public function sender_roles(): MorphToMany
	{
		return $this->morphedByMany(Role::class, 'sender', 'group_senders', 'group_id');
	}

	public function sender_voice_parts(): MorphToMany
	{
		return $this->morphedByMany(VoicePart::class, 'sender', 'group_senders', 'group_id');
	}

	public function sender_users(): MorphToMany
	{
		return $this->morphedByMany(User::class, 'sender', 'group_senders', 'group_id');
	}

	public function sender_singer_categories(): MorphToMany
	{
		return $this->morphedByMany(SingerCategory::class, 'sender', 'group_senders', 'group_id');
	}

	public function getEmailAttribute(): string
	{
		return $this->slug . '@' . $this->tenant->host;
	}

	/**
	 * @return Collection<User>
	 */
	public function get_all_senders(): Collection
	{
		// @todo use queries instead

		// Get directly-assigned users
		$users = $this->sender_users()->get();
		foreach ($this->sender_roles as $role) {
			$role_users = $role->users()->get();
			$users = $users->merge($role_users);
		}

		// Get users from voice parts
		$voice_part_ids = $this->sender_voice_parts()
			->get()
			->pluck('id')
			->toArray();
		$part_users = User::query()
			->whereHas('singer', function ($singer_query) use ($voice_part_ids) {
				$singer_query->whereIn('voice_part_id', $voice_part_ids);
			})
			->get();
		$users = $users->merge($part_users);

		// Get singers from categories
		$cat_ids = $this->sender_singer_categories()
			->get()
			->pluck('id');
		$category_users = User::query()
			->whereHas('singer', function ($singer_query) use ($cat_ids) {
				$singer_query->whereIn('singer_category_id', $cat_ids);
			})
			->get();
		$users = $users->merge($category_users);

		return $users->unique();
	}

	/**
	 * @param string $poly_class The class name of the polymorphic model
	 * @param string $poly_relationship The name of the other model's relationship to the polymorph
	 * @param string $poly_name The name of the polymorph used in table columns (x_id, x_type)
	 * @param string $related_id_col The name of the foreign key column connecting the polymorph to the other model
	 * @param array $poly_records An associative array where the keys are the model class names of each poly type and the values are arrays of ids to sync
	 */
	public function syncPolymorphicMany(
		string $poly_class,
		string $poly_relationship,
		string $poly_name,
		string $related_id_col,
		array $poly_records
	): void {
		foreach ($poly_records as $class => $records) {
			$this->syncPolymorhpic($poly_class, $poly_relationship, $poly_name, $related_id_col, $class, $records);
		}
	}

	/**
	 * @param string $poly_class The class name of the polymorphic model
	 * @param string $poly_relationship The name of the other model's relationship to the polymorph
	 * @param string $poly_name The name of the polymorph used in table columns (x_id, x_type)
	 * @param string $related_id_col The name of the foreign key column connecting the polymorph to the other model
	 * @param string $poly_type The model class name of type we're currently syncing
	 * @param int[]  $poly_ids The ids to sync
	 */
	public function syncPolymorhpic(
		string $poly_class,
		string $poly_relationship,
		string $poly_name,
		string $related_id_col,
		string $poly_type,
		array $poly_ids
	): void {
		// Detach the records not listed in the incoming array
		$poly_class
			::where($related_id_col, '=', $this->id)
			->where($poly_name . '_type', '=', $poly_type)
			->whereNotIn($poly_name . '_id', $poly_ids)
			->delete();

		// Insert new records
		$unchanged_ids = $poly_class
			::where($related_id_col, '=', $this->id)
			->where($poly_name . '_type', '=', $poly_type)
			->whereIn($poly_name . '_id', $poly_ids)
			->pluck($poly_name . '_id')
			->toArray();
		$new_poly_ids = array_diff($poly_ids, $unchanged_ids);

		$attach = [];
		foreach ($new_poly_ids as $new_poly_id) {
			$attach[] = [
				$poly_name . '_id' => $new_poly_id,
				$poly_name . '_type' => $poly_type,
			];
		}
		$this->fresh()
			->$poly_relationship()
			->createmany($attach);
	}

	public function authoriseSender(?User $user): bool
	{
		return match ($this->list_type) {
			'public' => true,
			'chat' => $user && $this->get_all_recipients()->contains($user),
			'distribution' => $user && $this->get_all_senders()->contains($user),
			default => false
		};
	}
}
