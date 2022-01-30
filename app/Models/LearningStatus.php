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

    protected $appends = ['status_name', 'status_colour', 'status_icon'];

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
            'not-started' => 'red-500',
            'assessment-ready' => 'yellow-500',
            'performance-ready' => 'green-500',
        };
    }

    public function getStatusIconAttribute(): string
    {
        return self::statusIcon($this->status);
    }

    public static function statusIcon(string $status): string
    {
        return match ($status) {
            'not-started' => 'fa-clock',
            'assessment-ready' => 'fa-check',
            'performance-ready' => 'fa-check-double',
        };
    }

    public static function getNullLearningStatus(): self
    {
        $nullStatus = new LearningStatus();
        $nullStatus->status = 'not-started';
        return $nullStatus;
    }
}
