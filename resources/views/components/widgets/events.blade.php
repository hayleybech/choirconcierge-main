@props(['events'])
@php use App\Models\Event; /* @var Event $event */ @endphp
<div class="card">
    <div class="card-header"><h3 class="h4">Upcoming Events</h3></div>
    <div class="list-group list-group-flush">
        @forelse($events as $event)
            <div class="list-group-item">
                @if( $event->call_time->isToday() )
                    <div class="d-flex align-items-center justify-content-between">
                        <h4>Today</h4>

                        @can('create', \App\Models\Attendance::class)
                            <a href="{{ route('events.attendances.index', ['event' => $event]) }}" class="btn btn-secondary btn-sm"><i class="fas fa-fw fa-edit"></i> Record Attendance</a>
                        @endcan
                    </div>
                    <div class="mb-2">
                        <strong>{{ $event->call_time->format('D, '.config('app.formats.time')) }}</strong>
                        - <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
                    </div>
                    <div>
                        <p>
                            <span class="place-name">{{ $event->location_name }}</span> <br>
                            <span class="place-address">{{ $event->location_address }}</span>
                        </p>
                        @if( $event->location_place_id )
                        <google-map api-key="{{ config('services.google.key')  }}" place-id="{{ urlencode($event->location_place_id) }}"></google-map>
                        @endif
                    </div>
                @else
                    <div>
                        {{ $event->call_time->format('D, '.config('app.formats.date_md')) }}
                         - <a href="{{ route('events.show', $event) }}">{{ $event->title }}</a>
                    </div>
                    <div>
                        <small><x-inline-rsvp :event="$event" :compact="true"></x-inline-rsvp></small>
                    </div>
                @endif
            </div>
        @empty
            <div class="list-group-item">No events this month.</div>
        @endforelse
    </div>
</div>