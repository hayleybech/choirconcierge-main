<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Event
 *
 * @property string title
 * @property EventType type
 * @property string start_date
 * @property string location
 *
 * @package App
 */
class Event extends Model
{
    public $dates = [
        'updated_at',
        'created_at',
        'start_date',
    ];

    public function type(): BelongsTo
    {
        return $this->belongsTo('App\EventType', 'type_id');
    }
}
