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
    <div class="r-table__cell column--actions">

        {{--
        @if ( Auth::user()->hasRole('Membership Team') )
            <form method="get" action="{{route( 'singer.move', ['singer' => $singer])}}" class="form-inline">
                <div class="input-group input-group-sm">
                    @php
                        echo Form::select('move_category', $categories_move,
                        0, ['class' => 'custom-select form-control-sm force-xs']);
                    @endphp

                    <div class="input-group-append">
                        <input type="submit" value="Move" class="btn btn-secondary btn-sm force-xs">
                    </div>
                </div>
            </form>
        @endif--}}
    </div>

</div>

