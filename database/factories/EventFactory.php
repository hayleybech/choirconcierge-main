<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    public function definition(): array
    {
        $call_time = Carbon::instance($this->faker->dateTimeBetween('now', '+1 year'));
        $start_time = (clone $call_time)->addHour();
        $end_time = (clone $start_time)->addHours(2);

        return [
            'title' => $this->faker->sentence(6, true),
            'call_time' => $call_time,
            'start_date' => $start_time,
            'end_date' => $end_time,
            'location_name' => $this->faker->sentence(3, true),
            'location_address' => $this->faker->address(), // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
            'description' => $this->faker->optional()->sentence(),
            'type_id' => EventType::where('title', 'Rehearsal')->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    public function repeating()
    {
        $total_repeats = $this->faker->numberBetween(4, 20);
        $repeat_unit = $this->faker->randomElement(['days', 'weeks', 'months']);

        return $this->state(function (array $attributes) use ($total_repeats, $repeat_unit) {
            return [
                'is_repeating' => true,
                'repeat_frequency_unit' => $repeat_unit,
                'repeat_until' => $attributes['call_time']->clone()->add($total_repeats.' '.$repeat_unit),
            ];
        });
    }
}
