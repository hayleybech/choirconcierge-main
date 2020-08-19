<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Rsvp
 *
 * Columns
 * @property int id
 * @property string response
 *
 * Relationships
 * @property Singer singer
 * @property Event event
 *
 * @package App\Models
 */
class Rsvp extends Model
{
    protected $fillable = [
        'singer_id',
        'response',
        'event_id',
    ];

    public function singer(): BelongsTo
    {
        return $this->belongsTo(Singer::class);
    }
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    public function getResponseStringAttribute(){
        return ucfirst($this->response);
    }
}
