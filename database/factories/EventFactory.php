<?php

namespace Database\Factories;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    /** @var string */
    protected $model = Event::class;

    public function definition(): array
    {
        $call_time = Carbon::instance( $this->faker->dateTimeThisYear() );
        $start_time = (clone $call_time)->addHour();
        $end_time = (clone $start_time)->addHours(2);

        return [
            'title' => $this->faker->sentence(6, true),
            'call_time' => $call_time,
            'start_date' => $start_time,
            'end_date' => $end_time,
            'location_name' => $this->faker->sentence(3, true),
            'location_address' => $this->faker->address, // @todo Use random REAL address for map testing (https://github.com/nonsapiens/addressfactory)
            'description' => $this->faker->optional()->sentence,
        ];
    }
}
