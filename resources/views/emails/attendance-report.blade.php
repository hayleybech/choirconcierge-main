<x-mail::message>
# Attendance Report
## {{ $event->title }}

<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }}**

The following attendance has been recorded for **{{ $event->title }}**:

<table width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td style="padding-right: 15px; padding-bottom: 15px;">
            <img src="{{ global_asset('/img/email/check.png') }}" alt="Present" height="16px" style="vertical-align: middle;"> <span style="color: #047857; font-weight: bold;">Present</span>: {{ $event->singers_attendance('present')->count() + $event->singers_attendance('late')->count() }}
        </td>
        <td style="padding-right: 15px;">
            <img src="{{ global_asset('/img/email/clock-solid-amber.png') }}" alt="Late" height="16px" style="vertical-align: middle;"> <span style="color: #d97706; font-weight: bold;">Late</span>: {{ $event->singers_attendance('late')->count() }}
        </td>
        <td>
            <img src="{{ global_asset('/img/email/times.png') }}" alt="Absent" height="16px" style="vertical-align: middle;"> <span style="color: #dc2626; font-weight: bold;">Absent</span>: {{ $event->singers_attendance('absent')->count() + $event->singers_attendance('absent_apology')->count() }}
        </td>
    </tr>
    @if($event->singers_attendance('late_deemed_absent')->count() > 0)
    <tr>
        <td colspan="3" style="text-align:center; padding-bottom: 15px;"><img src="{{ global_asset('/img/email/times.png') }}" alt="Absent" height="16px" style="vertical-align: middle;"> <span style="color: #dc2626; font-weight: bold;">Late (Deemed Absent)</span>: {{ $event->singers_attendance('late_deemed_absent')->count() }}</td>
    </tr>
        @endif
</table>

@if($absent_singers->isNotEmpty())
<h2>Absent Singers</h2>
@foreach($absent_singers as $ensemble => $voice_parts)
@if($show_ensembles)
<h3>{{ $ensemble }}</h3>
@endif
@foreach($voice_parts as $title => $data)
<h4 style="margin-bottom: 8px;">{{ $title }}</h4>
@foreach($data['singers'] as $membership)
@php
    $attendance = $membership->attendances->first();
@endphp
<div style="display: flex; align-items: center; margin-bottom: 4px;">
    <img src="{{ $membership->user->getAvatarUrl('thumb') }}" width="24" height="24" style="border-radius: 5px; margin-right: 8px;" alt="{{ $membership->user->name }}">
    <span>
        <strong>{{ $membership->user->name }}</strong>
        @if($attendance)
            <br><small style="color: #64748b;">{{ $attendance->label }}@if($attendance->absent_reason) &middot; {{ $attendance->absent_reason }}@endif</small>
        @endif
    </span>
</div>
@endforeach
@endforeach
@endforeach
@endif

<x-mail::button :url="$url">
View Full Report
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
