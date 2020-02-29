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
        {{ $event->start_date }}
    </div>
    <div class="r-table__cell column--location">
        {{ $event->location }}
    </div>
    <div class="r-table__cell column--created">
        {{ $event->created_at->diffForHumans() }}
    </div>
    <div class="r-table__cell column--actions">
        <a href="{{route( 'event.delete', ['event' => $event] )}}" class="link-confirm btn btn-outline-danger btn-sm ml-2"><i class="fa fa-fw fa-trash"></i></a>
    </div>

</div>

