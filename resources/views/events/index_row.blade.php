<tr class="row--event">
    <td class="col--title">
        <a href="{{route('events.show', ['event' => $event])}}">
        {{ ( isset($event->title) ) ? $event->title : 'Title Unknown' }}
        </a>
    </td>
    <td class="col--type">
        {{ $event->type->title }}
    </td>
    <td class="col--date">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $event->start_date->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $event->start_date->format('M d, H:i') }}
            </div>
        </div>
    </td>
    <td class="col--location">
        <span class="place-icon" style="background-image: url('{{ $event->location_icon }}');"></span> <span class="place-name">{{ $event->location_name }}</span>
    </td>
    <td class="col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $event->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $event->created_at->format('M d, H:i') }}
            </div>
        </div>
    </td>
    <td class="col--delete">
        @if(Auth::user()->hasRole('Music Team'))
            <x-delete-button :action="route( 'events.destroy', ['event' => $event] )"/>
        @endif
    </td>
</tr>

