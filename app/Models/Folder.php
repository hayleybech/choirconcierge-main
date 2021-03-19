<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * Relationships
 * @property Collection<Document> $documents
 *
 * @package App\Models
 */
class Folder extends Model
{
    use BelongsToTenant, SoftDeletes, HasFactory, TenantTimezoneDates;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
    ];

    public static function create( array $attributes = [] ) {
        /** @var Folder $folder */
        $folder = static::query()->create($attributes);

        return $folder;
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }
}
