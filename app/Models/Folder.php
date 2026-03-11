<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

        return $folder;
    }

    public function update(array $attributes = [], array $options = [])
    {
        parent::update($attributes, $options);

        // Sync ensembles
        $this->ensembles()->sync($attributes['ensembles'] ?? []);

        return true;
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function ensembles(): BelongsToMany
    {
        return $this->belongsToMany(Ensemble::class, 'ensemble_folder', 'folder_id', 'ensemble_id');
    }
}
