<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Event
 *
 * @property string title
 * @property EventType type
 * @property string start_date
 * @property string end_date
 * @property string call_time
 * @property string location_place_id
 * @property string location_icon
 * @property string location_name
 * @property string location_address
 * @property string description
 *
 * @package App
 */
class Event extends Model
{
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
}
