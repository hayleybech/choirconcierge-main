
<x-mail::message>
# New poll posted!
## {{ $poll->title }}

A new poll has been created. Please take a moment to cast your vote.

@if($poll->close_at)
<img src="{{ global_asset('/img/email/calendar-day.png') }}" alt="Date" height="16px"> **Closes at: {{ $poll->close_at->toDayDateTimeString() }}**
@endif

<x-mail::table>
 <x-mail::button-inline :url="$view_url">View Poll & Vote</x-mail::button-inline>
</x-mail::table>

Regards,
{{ config('app.name') }}
</x-mail::message>
