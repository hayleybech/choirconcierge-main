<?php

namespace App\Models;

use App\Models\Filters\Event_TypeFilter;
use App\Models\Filters\Filterable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Event
 *
 * Columns
 * @property int id
 * @property string title
 * @property int type_id
 * @property Carbon call_time
 * @property Carbon start_date
 * @property Carbon end_date
 * @property string location_place_id
 * @property string location_icon
 * @property string location_name
 * @property string location_address
 * @property string description
 * @property Carbon created_at
 * @property Carbon updated_at
 *
 * Relationships
 * @property EventType type
 *
 * @package App
 */
class Event extends Model
{
    use Filterable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'call_time',
        'location_place_id',
        'location_name',
        'location_address',
        'description',
    ];

    public $dates = [
        'updated_at',
        'created_at',
        'start_date',
        'end_date',
        'call_time',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo(EventType::class, 'type_id');
    }

    public static function initFilters(): void {
        self::$filters['type']   = new Event_TypeFilter();
    }
}
