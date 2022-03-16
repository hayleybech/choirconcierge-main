<?php

namespace App;

use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;

class EventIcalFeed
{
    public string $name = '';

    public string $description = '';

    public Calendar $calendar;

    public function __construct()
    {
        $this->name = 'Events for '.config('app.name');
        $this->description = '';

        $this->create();
        $this->addEvents();
    }

    private function create(): void
    {
        $this->calendar = Calendar::create()
            ->name($this->name)
            ->description($this->description)
            ->refreshInterval(5);
    }

    private function addEvents(): void
    {
        $events = Models\Event::all();
        foreach ($events as $event) {
            $ical_event = Event::create($event->title)
                ->description($event->description ?? '')
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
