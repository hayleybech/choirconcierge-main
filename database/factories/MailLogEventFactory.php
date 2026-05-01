<?php

namespace Database\Factories;

use App\Models\MailLog;
use App\Models\MailLogEvent;
use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MailLogEvent>
 */
class MailLogEventFactory extends Factory
{
    protected $model = MailLogEvent::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'mail_log_id' => MailLog::factory(),
            'status' => 'received',
            'context' => null,
            'user_group_id' => null,
        ];
    }
}
