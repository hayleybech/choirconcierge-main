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
 * @property Membership $singer
 * @property Song $song
 *
 * Attributes
 * @property string $status_name
 */
class LearningStatus extends Pivot
{
    protected $guarded = [];

    protected $appends = ['status_name'];

    public function getStatusNameAttribute(): string
    {
        return match ($this->status) {
            'not-started' => 'Learning',
            'assessment-ready' => 'Assessment Ready',
            'performance-ready' => 'Performance Ready',
        };
    }

    public static function getNullLearningStatus(): self
    {
        $nullStatus = new self();
        $nullStatus->status = 'not-started';

        return $nullStatus;
    }
}
