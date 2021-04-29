<?php

namespace Database\Factories;

use App\Models\NotificationTemplate;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationTemplateFactory extends Factory
{
    /** @var string */
    protected $model = NotificationTemplate::class;

    public function definition(): array
    {
        return [
	        'subject'       => $this->faker->sentence(),
	        'recipients'    => 'role:1',
	        'body'          => $this->faker->paragraph(),
	        'delay'         => $this->faker->numberBetween(2, 50).' '.$this->faker->randomElement(['seconds', 'minutes', 'hours', 'days', 'weeks', 'months']),
        ];
    }
}
