<div class="r-table__row row--song">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell song-col--title">
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
    <div class="r-table__cell song-col--created">
        {{ $song->created_at->diffForHumans() }}
    </div>
    <div class="r-table__cell col--delete">
        <a href="{{route( 'song.delete', ['song' => $song] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-times"></i></a>
    </div>

</div>

