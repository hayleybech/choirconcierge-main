<?php
namespace App\Http\Controllers;

use App\Models\Event;

class ICalController extends Controller
{
    public function index(): void {
        $events = Event::all();

        define('ICAL_FORMAT', 'Ymd\THis\Z');
        define('EOL', "\r\n");

        $icalObject =
            'BEGIN:VCALENDAR'.EOL.
            'VERSION:2.0'.EOL.
            'METHOD:PUBLISH'.EOL.
            'PRODID:-//Amplify Web Agency//Choir Concierge//EN'.EOL;

        // loop over events
        foreach ($events as $event) {
            $address = str_replace("\n", EOL, $event->location_address);

            $icalObject .=
            'BEGIN:VEVENT'.EOL.
            'DTSTART:'. date(ICAL_FORMAT, strtotime($event->call_time)) .EOL.
            'DTEND:'. date(ICAL_FORMAT, strtotime($event->end_date)) .EOL.
            'DTSTAMP:'. date(ICAL_FORMAT, strtotime($event->created_at)) .EOL.
            'SUMMARY:'. $event->title .EOL.
            'DESCRIPTION:'. $event->description .EOL.
            'UID:'. $event->id .EOL.
            'LAST-MODIFIED:' . date(ICAL_FORMAT, strtotime($event->updated_at)) .EOL.
            'LOCATION:'. $event->location_name. ', ' .$address .EOL.
            'END:VEVENT'.EOL;
        }

        // close calendar
        $icalObject .= "END:VCALENDAR";

        // Set the headers
        header('Cache-Control: no-cache, must-revalidate');
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="cal.ics"');
        header('Status: 200');

        echo $icalObject;
        exit;
    }
}