<div class="r-table__row">
    <div class="r-table__cell column--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell column--title">
        <a class="item-title" href="{{route('groups.show', ['group' => $group])}}">
        {{ $group->title ?? 'Title Unknown' }}
        </a>
    </div>
    <div class="r-table__cell column--slug">
        {{ $group->slug }}
    </div>
    <div class="r-table__cell column--type">
        {{ $group->list_type }}
    </div>
    <div class="r-table__cell column--created">
        <div class="date__diff-for-humans">
            {{ $group->created_at->diffForHumans() }}
        </div>
        <div class="date__regular">
            {{ $group->created_at->format('M d, H:i') }}
        </div>
    </div>
    <div class="r-table__cell column--actions">
        {{ Form::open( array( 'route' => ['groups.destroy', $group], 'method' => 'delete' ) ) }}
        <button class="link-confirm btn btn-outline-danger btn-sm ml-2"><i class="fa fa-fw fa-trash"></i></button>
        {{ Form::close() }}
    </div>

</div>

