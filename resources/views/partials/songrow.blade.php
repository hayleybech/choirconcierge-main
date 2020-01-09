<div class="r-table__row">
    <div class="r-table__cell column--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell column--title">
        {{ ( isset($song->title) ) ? $song->title : 'Title Unknown' }}
        {{--
        <div class="singer-name">
            <a href="{{route('singers.show', ['singer' => $singer])}}">
                {{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
            </a>
        </div>--}}
    </div>
    <div class="r-table__cell column--status">
        {{ $song->status->title }}
    </div>
    <div class="r-table__cell column--category">

    </div>
    <div class="r-table__cell column--pitch">
        {{ $song->pitch_blown }}
    </div>
    <div class="r-table__cell column--created">
        {{ $song->created_at->diffForHumans() }}
    </div>

</div>

