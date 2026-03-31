<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
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
    use BelongsToTenant, SoftDeletes, HasFactory, TenantTimezoneDates;

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

    public function viewer_singer_statuses(): MorphToMany
    {
        return $this->morphedByMany(SingerStatus::class, 'viewer', 'folder_viewers', 'folder_id');
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

    public function editor_singer_statuses(): MorphToMany
    {
        return $this->morphedByMany(SingerStatus::class, 'editor', 'folder_editors', 'folder_id');
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
            $this->syncPolymorphic($poly_class, $poly_relationship, $poly_name, $related_id_col, $class, $records);
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
    public function syncPolymorphic(
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
            ->where($poly_name.'_type', '=', $poly_type)
            ->whereNotIn($poly_name.'_id', $poly_ids)
            ->delete();

        // Insert new records
        $unchanged_ids = $poly_class
            ::where($related_id_col, '=', $this->id)
            ->where($poly_name.'_type', '=', $poly_type)
            ->whereIn($poly_name.'_id', $poly_ids)
            ->pluck($poly_name.'_id')
            ->toArray();
        $new_poly_ids = array_diff($poly_ids, $unchanged_ids);

        $attach = [];
        foreach ($new_poly_ids as $new_poly_id) {
            $attach[] = [
                $poly_name.'_id' => $new_poly_id,
                $poly_name.'_type' => $poly_type,
            ];
        }
        $this->fresh()
            ->$poly_relationship()
            ->createMany($attach);
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
        $status_ids = $this->$recipientType()
            ->get()
            ->pluck('id');

        return User::query()
            ->whereHas('memberships', fn ($singer_query) =>
            $singer_query->whereHas('statuses', fn ($query) => $query->whereIn('singer_statuses.id', $status_ids))
            )
            ->get();
    }
}
