
<x-mail::message>
# This event has been updated
## {{ $event->title }}

{{-- method 1: attach then reference--}}
{{--  $mailable->attachFromStorage('logo.png')  --}}
{{--<img src="cid:logo.png">--}}

{{-- method 2: path to public folder--}}

@if($event->wasChanged(['start_date', 'end_date']))
<h3 class="changed-heading">Date Changed</h3>
<x-mail::panel class="success">
@if($event->start_date->isSameDay($event->end_date))
<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }} - {{ $event->end_date->format('h:i A') }}**
@else
<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }} - {{ $event->end_date->toDayDateTimeString() }}**
@endif
</x-mail::panel>
@else
@if($event->start_date->isSameDay($event->end_date))
<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }} - {{ $event->end_date->format('h:i A') }}**
@else
<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **{{ $event->start_date->toDayDateTimeString() }} - {{ $event->end_date->toDayDateTimeString() }}**
@endif
@endif

@if($event->call_time)
@if($event->wasChanged(['call_time']))
<x-mail::panel class="success">
 <img src="{{ global_asset('/img/email/clock-solid.png') }}" alt="Call time" height="16px"> **Updated Call Time Time:** {{ $event->call_time->format('h:i A') }}
</x-mail::panel>
@else
<img src="{{ global_asset('/img/email/clock-solid.png') }}" alt="Call time" height="16px"> **Call Time:** {{ $event->call_time->format('h:i A') }}
@endif
@endif

@if($event->location_name || $event->location_address)
@if($event->wasChanged(['location_name', 'location_address']))
<h3 class="changed-heading">Location Changed</h3>
<x-mail::panel class="success">
<img src="{{ global_asset('/img/email/map-marker-alt.png') }}" alt="Location" height="16px"> @if($event->location_name)**{{ $event->location_name }}** -@endif {{ $event->location_address }}
</x-mail::panel>
@else
<img src="{{ global_asset('/img/email/map-marker-alt.png') }}" alt="Location" height="16px"> @if($event->location_name)**{{ $event->location_name }}** -@endif {{ $event->location_address }}
@endif
@endif

<x-mail::table>
 <x-mail::button-inline :url="$view_url">View Event</x-mail::button-inline>  <x-mail::button-inline :url="$going_url" color="success"><img src="{{ global_asset('/img/email/check.png') }}" alt="Going" height="16px"> Going</x-mail::button-inline>  <x-mail::button-inline :url="$not_going_url" color="error"><img src="{{ global_asset('/img/email/times.png') }}" alt="Not Going" height="16px"> Not Going</x-mail::button-inline>
</x-mail::table>

### Description
@if($event->wasChanged('description'))
{{ new \Illuminate\Support\HtmlString($description_diff) }}
@else
{{ new \Illuminate\Support\HtmlString($event->description) }}
@endif

{{ $tracking_pixel ?? '' }}

Regards, <br />
{{ config('app.name') }}
</x-mail::message>