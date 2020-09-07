<?php


namespace App\Http\Controllers;


use App\Models\Event;
use App\Models\Singer;
use App\Models\VoicePart;

class AttendanceReportController
{
    public function __invoke()
    {
        $all_events = Event::with([])
            ->orderBy('start_date')
            ->filter()
            ->get();

        $voice_parts = VoicePart::with(['singers', 'singers.attendances'])->get();
        $no_part = new VoicePart();
        $no_part->title = 'No Part';
        $no_part->singers = Singer::whereDoesntHave('voice_part')->get();
        $voice_parts[] = $no_part;

        return view('events.reports.attendance', [
            'voice_parts' => $voice_parts,
            'events' => $all_events->where('start_date', '<', now()),
            'filters' => Event::getFilters(),
        ]);
    }
}