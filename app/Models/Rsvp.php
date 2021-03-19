<?php

namespace App\Models;

use App\Models\Traits\TenantTimezoneDates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Class Rsvp
 *
 * Columns
 * @property int $id
 * @property string $response
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Singer $singer
 * @property Event $event
 *
 * @package App\Models
 */
class Rsvp extends Model
{
    use TenantTimezoneDates;

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
