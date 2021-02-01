<?php


namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class Attendance
 *
 * Columns
 * @property int id
 * @property string response
 * @property string absent_reason
 *
 * Relationships
 * @property Singer singer
 * @property Event event
 *
 * @package App\Models
 */
class Attendance extends Model
{
    protected $fillable = [
        'singer_id',
        'response',
        'absent_reason',
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
        return ($this->response === 'absent_apology') ? 'Absent (With Apology)' : ucfirst($this->response);
    }
}