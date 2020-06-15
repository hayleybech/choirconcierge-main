<div class="r-table__row row--event">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a class="item-title" href="{{route('events.show', ['event' => $event])}}">
        {{ ( isset($event->title) ) ? $event->title : 'Title Unknown' }}
        </a>
    </div>
    <div class="r-table__cell event-col--type">
        <span class="badge badge-dark">{{ $event->type->title }}</span>
    </div>
    <div class="r-table__cell event-col--date">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $event->start_date->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $event->start_date->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell event-col--location">
        <span class="place-icon" style="background-image: url('{{ $event->location_icon }}');"></span> <span class="place-name">{{ $event->location_name }}</span>
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $event->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $event->created_at->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell col--delete">
        @if(Auth::user()->hasRole('Music Team'))
        <a href="{{route( 'event.delete', ['event' => $event] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-trash"></i></a>
        @endif
    </div>

</div>

