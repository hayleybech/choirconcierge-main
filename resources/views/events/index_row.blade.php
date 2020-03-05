<div class="r-table__row">
    <div class="r-table__cell column--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell column--title">
        <a class="item-title" href="{{route('events.show', ['event' => $event])}}">
        {{ ( isset($event->title) ) ? $event->title : 'Title Unknown' }}
        </a>
    </div>
    <div class="r-table__cell column--type">
        {{ $event->type->title }}
    </div>
    <div class="r-table__cell column--start-date">
        <div class="date__diff-for-humans">
            {{ $event->start_date->diffForHumans() }}
        </div>
        <div class="date__regular">
            {{ $event->start_date->format('M d, H:i') }}
        </div>
    </div>
    <div class="r-table__cell column--location">
        {{ $event->location }}
    </div>
    <div class="r-table__cell column--created">
        <div class="date__diff-for-humans">
            {{ $event->created_at->diffForHumans() }}
        </div>
        <div class="date__regular">
            {{ $event->created_at->format('M d, H:i') }}
        </div>
    </div>
    <div class="r-table__cell column--actions">
        <a href="{{route( 'event.delete', ['event' => $event] )}}" class="link-confirm btn btn-outline-danger btn-sm ml-2"><i class="fa fa-fw fa-trash"></i></a>
    </div>

</div>

