<tr class="row--song">
    <td class="col--title">
        @can('view', $song)
        <a href="{{route('songs.show', ['song' => $song])}}">
        {{ $song->title ?? 'Title Unknown' }}
        </a>
        @else
        {{ $song->title ?? 'Title Unknown' }}
        @endcan
    </td>
    @if($col_status)
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
    <td class="col--status {{ $category_colour }} font-weight-bold">
        <i class="fas fa-fw fa-circle mr-2"></i><span class="status__title">{{ $song->status->title }}</span>
    </td>
    @endif
    <td class="col--category">
        @foreach( $song->categories as $cat )
        <span class="badge badge-dark mr-1">{{ $cat->title }}</span>
        @endforeach
    </td>
    <td class="col--pitch">
        <pitch-button note="{{ explode('/',$song->pitch)[0] }}"></pitch-button>
    </td>
    <td class="col--created">
        <div class="date">
            <div class="date__diff-for-humans">
                {{ $song->created_at->diffForHumans() }}
            </div>
            <div class="date__regular">
                {{ $song->created_at->format('M d, H:i') }}
            </div>
        </div>
    </td>
    <td class="col--delete">
        @can('delete', $song)
        <x-delete-button :action="route( 'songs.destroy', ['song' => $song] )" :message="$song->attachments()->count() . ' attachments will also be deleted.'"/>
        @endcan
    </td>

</tr>