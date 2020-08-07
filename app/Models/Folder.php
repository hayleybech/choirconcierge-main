<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

/**
 * Class Folder
 *
 * Columns
 * @property int $id
 * @property string $title
 *
 * @package App\Models
 */
class Folder extends Model
{
    use BelongsToTenant;

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
