<div class="r-table__row row--song">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a class="item-title" href="{{route('folders.show', ['folder' => $folder])}}">
        {{ ( isset($folder->title) ) ? $folder->title : 'Title Unknown' }}
        </a>
    </div>
    <div class="r-table__cell song-col--status">
        {{ $folder->documents->count() }} documents
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $folder->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $folder->created_at->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell col--delete">
        @if( Auth::user()->isEmployee() )
        <a href="{{route( 'folders.delete', ['folder' => $folder] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-times"></i></a>
        @endif
    </div>

</div>

