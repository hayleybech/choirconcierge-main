
<x-mail::message>
# New event posted!
## {{ $event->title }}

{{-- method 1: attach then reference--}}
{{--  $mailable->attachFromStorage('logo.png')  --}}
{{--<img src="cid:logo.png">--}}

{{-- method 2: path to public folder--}}

@if($event->start_date->isSameDay($event->end_date))
<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }} - {{ $event->end_date->format('h:i A') }}**
@else
<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }} - {{ $event->end_date->toDayDateTimeString() }}**
@endif

@if($event->call_time)
  <img src="{{ global_asset('/img/email/clock-solid.png') }}" alt="Call time" height="16px"> **Call Time:** {{ $event->call_time->toTimeString() }}
@endif

@if($event->location_name || $event->location_address)
<img src="{{ global_asset('/img/email/map-marker-alt.png') }}" alt="Location" height="16px"> @if($event->location_name)**{{ $event->location_name }}** -@endif {{ $event->location_address }}
@endif

<x-mail::table>
 <x-mail::button-inline :url="$view_url">View Event</x-mail::button-inline>  <x-mail::button-inline :url="$going_url" color="success"><img src="{{ global_asset('/img/email/check.png') }}" alt="Going" height="16px"> Going</x-mail::button-inline>  <x-mail::button-inline :url="$not_going_url" color="error"><img src="{{ global_asset('/img/email/times.png') }}" alt="Not Going" height="16px"> Not Going</x-mail::button-inline>
</x-mail::table>

### Description
{{ new \Illuminate\Support\HtmlString($event->description) }}

{{ $tracking_pixel ?? '' }}

Regards,
{{ config('app.name') }}
</x-mail::message>