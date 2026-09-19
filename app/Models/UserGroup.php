<?php

namespace App\Models;

use App\Models\Traits\SyncsPolymorphicRelationships;
use App\Models\Traits\TenantTimezoneDates;
use App\Enums\SingerStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
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
 * @property Collection<SingerStatus> $recipient_singer_categories
 *
 * @property Collection<GroupSender> $senders
 * @property Collection<Role> $sender_roles
 * @property Collection<User> $sender_users
 * @property Collection<VoicePart> $sender_voice_parts
 * @property Collection<SingerStatus> $sender_singer_categories
 *
 * Attributes
 * @property string $email
 */
class UserGroup extends Model
{
    use BelongsToTenant, SoftDeletes, HasFactory, TenantTimezoneDates, SyncsPolymorphicRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title', 'slug', 'list_type'];

    protected $appends = ['email', 'type_icon'];

    public static function create(array $attributes = [])
    {
        /** @var UserGroup $group */
        $group = static::query()->create($attributes);

        // Update recipients
        $group->syncPolymorphicMany(GroupMember::class, 'members', 'memberable', 'group_id', [
            Role::class => $attributes['recipient_roles'] ?? [],
            VoicePart::class => $attributes['recipient_voice_parts'] ?? [],
            User::class => $attributes['recipient_users'] ?? [],
            SingerStatus::class => $attributes['recipient_singer_statuses'] ?? [],
            Ensemble::class => $attributes['recipient_ensembles'] ?? [],
        ]);

        // Update senders
        $group->syncPolymorphicMany(GroupSender::class, 'senders', 'sender', 'group_id', [
            Role::class => $attributes['sender_roles'] ?? [],
            VoicePart::class => $attributes['sender_voice_parts'] ?? [],
            User::class => $attributes['sender_users'] ?? [],
            SingerStatus::class => $attributes['sender_singer_statuses'] ?? [],
            Ensemble::class => $attributes['sender_ensembles'] ?? [],
        ]);

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
            SingerStatus::class => $attributes['recipient_singer_statuses'] ?? [],
            Ensemble::class => $attributes['recipient_ensembles'] ?? [],
        ]);

        // Update senders
        $this->syncPolymorphicMany(GroupSender::class, 'senders', 'sender', 'group_id', [
            Role::class => $attributes['sender_roles'] ?? [],
            VoicePart::class => $attributes['sender_voice_parts'] ?? [],
            User::class => $attributes['sender_users'] ?? [],
            SingerStatus::class => $attributes['sender_singer_statuses'] ?? [],
            Ensemble::class => $attributes['sender_ensembles'] ?? [],
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

    public function recipient_singer_statuses(): HasMany
    {
        return $this->hasMany(GroupMember::class, 'group_id')
            ->where('memberable_type', SingerStatus::class);
    }

    public function recipient_ensembles(): MorphToMany
    {
        return $this->morphedByMany(Ensemble::class, 'memberable', 'group_members', 'group_id');
    }

    public function scopeByEmail(Builder $query, string $email): Builder
    {
        [$slug, $host] = explode('@', $email);

        return $query->whereHas('tenant', fn(Builder $query) => $query
            ->whereHas('domains', fn(Builder $query) => $query
                ->where('domain', explode('.', $host)[0])
            )->orWhere('id', explode('.', $host)[0])
        )->where('slug', $slug);
    }

    public function get_all_recipients(): \Illuminate\Support\Collection
    {
        tenancy()->initialize($this->tenant);

        $recipients = $this->recipient_users()->get()
            ->merge($this->getRoleUsers())
            ->merge($this->getPartUsers())
            ->merge($this->getStatusUsers());

        $ensembles = $this->recipient_ensembles;
        if ($ensembles->isNotEmpty()) {
            $ensembleUserIds = User::query()
                ->whereHas('memberships', fn ($query) => $query
                    ->active()
                    ->whereHas('enrolments', fn ($query) => $query
                        ->whereIn('ensemble_id', $ensembles->pluck('id'))
                    )
                )
                ->pluck('id');

            $recipients = $recipients->whereIn('id', $ensembleUserIds);
        }

        return $recipients->unique();
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

    public function sender_singer_statuses(): HasMany
    {
        return $this->hasMany(GroupSender::class, 'group_id')
            ->where('sender_type', SingerStatus::class);
    }
    
    public function sender_ensembles(): MorphToMany
    {
        return $this->morphedByMany(Ensemble::class, 'sender', 'group_senders', 'group_id');
    }

    public function mail_log_events(): HasMany
    {
        return $this->hasMany(MailLogEvent::class);
    }

    public function getEmailAttribute(): string
    {
        return $this->slug.'@'.$this->tenant->host;
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->list_type) {
            'public' => 'fa-envelope-open-text',
            'chat' => 'fa-comments',
            'distribution' => 'fa-paper-plane',
        };
    }

    /**
     * @return Collection<User>
     */
    public function get_all_senders(): \Illuminate\Support\Collection
    {
        tenancy()->initialize($this->tenant);

        $senders = $this->sender_users()->get()
            ->merge($this->getRoleUsers('sender_roles'))
            ->merge($this->getPartUsers('sender_voice_parts'))
            ->merge($this->getStatusUsers('sender_singer_statuses'));

        $ensembles = $this->sender_ensembles;
        if ($ensembles->isNotEmpty()) {
            $ensembleUserIds = User::query()
                ->whereHas('memberships', fn ($query) => $query
                    ->active()
                    ->whereHas('enrolments', fn ($query) => $query
                        ->whereIn('ensemble_id', $ensembles->pluck('id'))
                    )
                )
                ->pluck('id');

            $senders = $senders->whereIn('id', $ensembleUserIds);
        }

        return $senders->unique();
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

    private function getRoleUsers(string $recipientType = 'recipient_roles'): \Illuminate\Support\Collection
    {
        // @todo use queries instead
        return $this->$recipientType
            ->flatMap(fn($role) => $role->members()->with('user')->active()->get()
                ->map(fn($singer) => $singer->user));
    }

    private function getPartUsers(string $recipientType = 'recipient_voice_parts'): \Illuminate\Support\Collection
    {
        $voice_part_ids = $this->$recipientType()
            ->get()
            ->pluck('id')
            ->toArray();

        return User::query()
            ->whereHas('memberships', fn ($query) => $query
                ->active()
                ->whereHas('enrolments', fn ($query) => $query
                    ->whereIn('voice_part_id', $voice_part_ids)
                )
            )
            ->get();
    }

    private function getStatusUsers(string $recipientType = 'recipient_singer_statuses'): \Illuminate\Support\Collection
    {
        $idCol = str_contains($recipientType, 'sender') ? 'sender_id' : 'memberable_id';
        $status_slugs = $this->$recipientType()
            ->get()
            ->pluck($idCol);

        return User::query()
            ->whereHas('memberships', fn ($singer_query) =>
                $singer_query->whereHas('status', fn ($query) => $query
                    ->whereIn('membership_status.status', $status_slugs)
                )
            )
            ->get();
    }
}
