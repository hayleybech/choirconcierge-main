<div class="r-table__row row--song">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a class="item-title" href="{{route('songs.show', ['song' => $song])}}">
        {{ ( isset($song->title) ) ? $song->title : 'Title Unknown' }}
        </a>
    </div>
    <div class="r-table__cell song-col--status">
        <span class="badge badge-dark">{{ $song->status->title }}</span>
    </div>
    <div class="r-table__cell song-col--category">
        @foreach( $song->categories as $cat )
        <span class="badge badge-secondary mr-1">{{ $cat->title }}</span>
        @endforeach
    </div>
    <div class="r-table__cell song-col--pitch">
        {{ $song->pitch }}
    </div>
    <div class="r-table__cell col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $song->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $song->created_at->format('M d, H:i') }}
            </div>
        </div>
    </div>
    <div class="r-table__cell col--delete">
        @if(Auth::user()->hasRole('Music Team'))
        <a href="{{route( 'songs.delete', ['song' => $song] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-trash"></i></a>
        @endif
    </div>

</div>

