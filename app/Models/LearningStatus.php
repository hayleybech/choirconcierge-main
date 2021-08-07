<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Carbon;

/**
 * Class LearningStatus
 *
 * Columns
 * @property string $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * Relationships
 * @property Singer $singer
 * @property Song $song
 *
 * Attributes
 * @property string $status_name
 */
class LearningStatus extends Pivot
{
    protected $guarded = [];

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'not-started' => 'Learning',
            'assessment-ready' => 'Assessment Ready',
            'performance-ready' => 'Performance Ready',
        };
    }

    public function getStatusColourAttribute(): string
    {
        return match ($this->status) {
            'not-started' => 'danger',
            'assessment-ready' => 'warning',
            'performance-ready' => 'success',
        };
    }

    public static function getNullLearningStatus(): self
    {
        $nullStatus = new LearningStatus();
        $nullStatus->status = 'not-started';
        return $nullStatus;
    }
}
