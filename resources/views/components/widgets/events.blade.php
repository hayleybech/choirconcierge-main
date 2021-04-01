@props(['events'])
@php use App\Models\Event; /* @var Event $event */ @endphp
<div class="card">
    <div class="card-header"><h3 class="h4">Upcoming Events</h3></div>
    <div class="list-group list-group-flush">
        @forelse($events as $event)
            <div class="list-group-item">
                {{ $event->call_time->format('D, '.config('app.formats.date_md')) }} - <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
            </div>
        @empty
            <div class="list-group-item">No events this month.</div>
        @endforelse
    </div>
</div>