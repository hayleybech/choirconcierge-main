<div class="r-table__row row--event">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a class="item-title" href="{{route('stacks.show', ['stack' => $stack])}}">
        {{ ( isset($stack->title) ) ? $stack->title : 'Title Unknown' }}
        </a>
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $stack->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $stack->created_at->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell col--delete">
        @if(Auth::user()->hasRole('Music Team'))
        <a href="{{route( 'stacks.destroy', ['stack' => $stack] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-trash"></i></a>
        @endif
    </div>

</div>

