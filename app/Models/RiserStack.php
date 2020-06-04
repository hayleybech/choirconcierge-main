<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * Class RiserStack
 *
 * Columns
 * @property int id
 * @property string title
 * @property int rows
 * @property int columns
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * Relationships
 * @property Singer[] singers
 *
 * @package App\Models
 */
class RiserStack extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'rows',
        'columns',
    ];

    public function singers(): BelongsToMany
    {
        return $this->belongsToMany(Singer::class)
            ->as('position')
            ->withPivot('row', 'column');
    }
}
