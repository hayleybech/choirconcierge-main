<tr class="row--event">
    <td class="col--title">
        <a href="{{route('events.show', ['event' => $event])}}">
        {{ ( isset($event->title) ) ? $event->title : 'Title Unknown' }}
        </a>
        @if( $event->is_repeating )
            <i class="fal fa-fw fa-repeat" title="Repeating Event"></i>
        @endif
    </td>
    <td class="col--type">
        {{ $event->type->title }}
    </td>
    <td class="col--date">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $event->call_time->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $event->call_time->format(config('app.formats.timestamp_md')) }}
            </div>
        </div>
    </td>
    <td class="col--location">
        <span class="place-icon" style="background-image: url('{{ $event->location_icon }}');"></span> <span class="place-name">{{ $event->location_name }}</span>
    </td>
    @if($col_rsvp)
        @can('viewAny', \App\Models\Rsvp::class)
            <td class="col--rsvp">{{ $event->going_count }} going</td>
        @endcan
    @endif
    @if($col_attendance)
        @can('viewAny', \App\Models\Attendance::class)
        <td class="col--attendance">{{ $event->present_count }} present</td>
        @endcan
    @endif
    <td class="col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $event->created_at?->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $event->created_at?->format(config('app.formats.timestamp_md')) }}
            </div>
        </div>
    </td>
    <td class="col--delete">
        @can('delete', $event)
            @if($event->is_repeating)
                <repeating-event-delete-button
                    route="{{ route('events.delete-recurring', ['event' => $event, 'mode' => '--replace--']) }}"
                    event-id="{{ $event->id }}"
                    event-title="{{ $event->title }}"
                    :event-in-past="{{ json_encode($event->in_past, JSON_THROW_ON_ERROR) }}"
                    :event-is-parent="{{ json_encode($event->is_repeat_parent, JSON_THROW_ON_ERROR) }}"
                    :parent-in-past="{{ json_encode(optional($event->repeat_parent)->in_past ?? null, JSON_THROW_ON_ERROR) }}"
                ></repeating-event-delete-button>
            @else
                <x-delete-button :action="route( 'events.destroy', ['event' => $event] )"/>
            @endif
        @endcan
    </td>
</tr>

