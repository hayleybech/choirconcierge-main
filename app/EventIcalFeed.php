<?php

namespace App;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Illuminate\Database\Eloquent\Collection;

class EventIcalFeed
{
    public string $name = '';

    public string $description = '';

    public Calendar $calendar;

    public function __construct(Collection $events, bool $legacy = false)
    {
        $this->name = 'Events for '.config('app.name');
        $this->description = '';

        $this->create();
        $this->addEvents($events, $legacy);
    }

    private function create(): void
    {
        $this->calendar = Calendar::create()
            ->name($this->name)
            ->description($this->description)
            ->refreshInterval(5);
    }

    private function addEvents(Collection $events, bool $legacy): void
    {
        foreach ($events as $event) {
            $ical_event = Event::create(($legacy ? '⚠ ' : '').$event->title)
                ->description($legacy ? 'This version of the calendar sync will stop working December 2026. Please log in to Choir Concierge to get your new calendar sync URL. ' : ($event->description ?? ''))
                ->createdAt($event->created_at ?? now())
                ->startsAt($event->call_time)
                ->endsAt($event->end_date)
                ->addressName($event->location_name ?? '')
                ->address($event->location_address ?? '');
            $this->calendar->event($ical_event);
        }
    }

    public function get(): string
    {
        return $this->calendar->get();
    }
}
