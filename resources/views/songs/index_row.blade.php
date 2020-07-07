<div class="r-table__row row--song">
    <div class="r-table__cell col--mark">
        <input type="checkbox" />
    </div>
    <div class="r-table__cell col--title">
        <a class="item-title" href="{{route('songs.show', ['song' => $song])}}">
        {{ ( isset($song->title) ) ? $song->title : 'Title Unknown' }}
        </a>
    </div>
    <?php
    $category_colour = '';
    if($song->status->title === 'Pending') {
        $category_colour = 'text-danger';
    } elseif($song->status->title === 'Learning') {
        $category_colour = 'text-primary';
    } elseif ($song->status->title === 'Active') {
        $category_colour = 'text-success';
    } elseif($song->status->title === 'Archived') {
        $category_colour = 'text-secondary';
    } else {
        $category_colour = '';
    }
    ?>
    <div class="r-table__cell song-col--status {{ $category_colour }} font-weight-bold">
        <i class="fas fa-fw fa-circle mr-2"></i>{{ $song->status->title }}
    </div>
    <div class="r-table__cell song-col--category">
        @foreach( $song->categories as $cat )
        <span class="badge badge-pill badge-secondary mr-1">{{ $cat->title }}</span>
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
            <x-delete-button :action="route( 'songs.destroy', ['song' => $song] )" :message="$song->attachments()->count() . ' attachments will also be deleted.'"/>
        @endif
    </div>

</div>

