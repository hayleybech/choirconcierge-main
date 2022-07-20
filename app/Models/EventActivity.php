<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Columns
 * @property int $id
 * @property int $event_id
 * @property string $description
 * @property int $duration
 */

class EventActivity extends Model
{
    use HasFactory;

    protected $guarded = [];
}
