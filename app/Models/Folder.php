<?php

namespace App\Models;

use App\Models\Traits\SyncsPolymorphicRelationships;
use App\Models\Traits\TenantTimezoneDates;
use App\Enums\SingerStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Folder
 *
 * Columns
 * @property int $id
 * @property string $title
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @property int $tenant_id
 *
 * Relationships
 * @property Collection<Document> $documents
 * @property Collection<Ensemble> $ensembles
 * @property Collection<Role> $viewer_roles
 * @property Collection<VoicePart> $viewer_voice_parts
 * @property Collection<User> $viewer_users
 * @property Collection<SingerStatus> $viewer_singer_statuses
 * @property Collection<Role> $editor_roles
 * @property Collection<VoicePart> $editor_voice_parts
 * @property Collection<User> $editor_users
 * @property Collection<SingerStatus> $editor_singer_statuses
 */
class Folder extends Model
{
    use BelongsToTenant, SoftDeletes, HasFactory, TenantTimezoneDates, SyncsPolymorphicRelationships;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['title'];

    protected $with = ['ensembles'];

    public static function create(array $attributes = [])
    {
        $ensembles = $attributes['ensembles'] ?? [];

        unset($attributes['ensembles']);

        /** @var Folder $folder */
        $folder = static::query()->create($attributes);

        // Attach ensembles
        $folder->ensembles()->attach($ensembles);

        // Sync viewers
        $folder->syncPolymorphicMany(FolderViewer::class, 'viewers', 'viewer', 'folder_id', [
            Role::class => $attributes['viewer_roles'] ?? [],
            VoicePart::class => $attributes['viewer_voice_parts'] ?? [],
            User::class => $attributes['viewer_users'] ?? [],
            SingerStatus::class => $attributes['viewer_singer_statuses'] ?? [],
        ]);

        // Sync editors
        $folder->syncPolymorphicMany(FolderEditor::class, 'editors', 'editor', 'folder_id', [
            Role::class => $attributes['editor_roles'] ?? [],
            VoicePart::class => $attributes['editor_voice_parts'] ?? [],
            User::class => $attributes['editor_users'] ?? [],
            SingerStatus::class => $attributes['editor_singer_statuses'] ?? [],
        ]);

        return $folder;
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Sync ensembles
        $this->ensembles()->sync($attributes['ensembles'] ?? []);

        // Sync viewers
        $this->syncPolymorphicMany(FolderViewer::class, 'viewers', 'viewer', 'folder_id', [
            Role::class => $attributes['viewer_roles'] ?? [],
            VoicePart::class => $attributes['viewer_voice_parts'] ?? [],
            User::class => $attributes['viewer_users'] ?? [],
            SingerStatus::class => $attributes['viewer_singer_statuses'] ?? [],
        ]);

        // Sync editors
        $this->syncPolymorphicMany(FolderEditor::class, 'editors', 'editor', 'folder_id', [
            Role::class => $attributes['editor_roles'] ?? [],
            VoicePart::class => $attributes['editor_voice_parts'] ?? [],
            User::class => $attributes['editor_users'] ?? [],
            SingerStatus::class => $attributes['editor_singer_statuses'] ?? [],
        ]);

        return true;
    }

    public function viewers(): HasMany
    {
        return $this->hasMany(FolderViewer::class, 'folder_id');
    }

    public function viewer_roles(): MorphToMany
    {
        return $this->morphedByMany(Role::class, 'viewer', 'folder_viewers', 'folder_id');
    }

    public function viewer_voice_parts(): MorphToMany
    {
        return $this->morphedByMany(VoicePart::class, 'viewer', 'folder_viewers', 'folder_id');
    }

    public function viewer_users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'viewer', 'folder_viewers', 'folder_id');
    }

    public function viewer_singer_statuses(): HasMany
    {
        return $this->hasMany(FolderViewer::class, 'folder_id')
            ->where('viewer_type', SingerStatus::class);
    }

    public function editors(): HasMany
    {
        return $this->hasMany(FolderEditor::class, 'folder_id');
    }

    public function editor_roles(): MorphToMany
    {
        return $this->morphedByMany(Role::class, 'editor', 'folder_editors', 'folder_id');
    }

    public function editor_voice_parts(): MorphToMany
    {
        return $this->morphedByMany(VoicePart::class, 'editor', 'folder_editors', 'folder_id');
    }

    public function editor_users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'editor', 'folder_editors', 'folder_id');
    }

    public function editor_singer_statuses(): HasMany
    {
        return $this->hasMany(FolderEditor::class, 'folder_id')
            ->where('editor_type', SingerStatus::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function ensembles(): BelongsToMany
    {
        return $this->belongsToMany(Ensemble::class, 'ensemble_folder', 'folder_id', 'ensemble_id');
    }

    /**
     * @return Collection<User>
     */
    public function get_all_viewers(): Collection
    {
        $viewers = $this->viewer_users()->get()
            ->merge($this->getRoleUsers('viewer_roles'))
            ->merge($this->getPartUsers('viewer_voice_parts'))
            ->merge($this->getStatusUsers('viewer_singer_statuses'));

        $ensembles = $this->ensembles;
        if ($ensembles->isNotEmpty()) {
            $ensembleUserIds = User::query()
                ->whereHas('memberships', fn ($query) => $query
                    ->active()
                    ->whereHas('enrolments', fn ($query) => $query
                        ->whereIn('ensemble_id', $ensembles->pluck('id'))
                    )
                )
                ->pluck('id');

            $viewers = $viewers->whereIn('id', $ensembleUserIds);
        }

        return $viewers->unique();
    }

    /**
     * @return Collection<User>
     */
    public function get_all_editors(): Collection
    {
        $editors = $this->editor_users()->get()
            ->merge($this->getRoleUsers('editor_roles'))
            ->merge($this->getPartUsers('editor_voice_parts'))
            ->merge($this->getStatusUsers('editor_singer_statuses'));

        $ensembles = $this->ensembles;
        if ($ensembles->isNotEmpty()) {
            $ensembleUserIds = User::query()
                ->whereHas('memberships', fn ($query) => $query
                    ->active()
                    ->whereHas('enrolments', fn ($query) => $query
                        ->whereIn('ensemble_id', $ensembles->pluck('id'))
                    )
                )
                ->pluck('id');

            $editors = $editors->whereIn('id', $ensembleUserIds);
        }

        return $editors->unique();
    }


    private function getRoleUsers(string $recipientType): Collection
    {
        return $this->$recipientType
            ->flatMap(fn($role) => $role->members()->with('user')->active()->get()
                ->map(fn($singer) => $singer->user));
    }

    private function getPartUsers(string $recipientType): Collection
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

    private function getStatusUsers(string $recipientType): Collection
    {
        $idCol = str_contains($recipientType, 'editor') ? 'editor_id' : 'viewer_id';
        $status_slugs = $this->$recipientType()
            ->get()
            ->pluck($idCol);

        return User::query()
            ->whereHas('memberships', fn ($singer_query) =>
                $singer_query->whereHas('status', fn ($query) => $query->whereIn('membership_status.status', $status_slugs))
            )
            ->get();
    }
}
