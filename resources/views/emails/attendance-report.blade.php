<x-mail::message>
# Attendance Report
## {{ $event->title }}

<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }}**

The following attendance has been recorded for **{{ $event->title }}**:

<img src="{{ global_asset('/img/email/check.png') }}" alt="Present" height="16px"> <span style="color: #047857; font-weight: bold;">Total Present</span>: {{ $event->singers_attendance('present')->count() + $event->singers_attendance('late')->count() }}

<img src="{{ global_asset('/img/email/clock-solid-amber.png') }}" alt="Late" height="16px"> <span style="color: #d97706; font-weight: bold;">Late</span>: {{ $event->singers_attendance('late')->count() }}

<img src="{{ global_asset('/img/email/times.png') }}" alt="Absent" height="16px"> <span style="color: #dc2626; font-weight: bold;">Absent</span>: {{ $event->singers_attendance('absent')->count() + $event->singers_attendance('absent_apology')->count() }}

@if($event->singers_attendance('late_deemed_absent')->count() > 0)
<img src="{{ global_asset('/img/email/times.png') }}" alt="Late (Deemed Absent)" height="16px"> <span style="color: #dc2626; font-weight: bold;">Late (Deemed Absent)</span>: {{ $event->singers_attendance('late_deemed_absent')->count() }}
@endif

<x-mail::button :url="$url">
View Full Report
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
