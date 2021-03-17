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
    <td class="col--status text-{{ $song->status->colour }} font-weight-bold">
        @if('Pending' === $song->status->title)
        <i class="fas fa-fw fa-lock mr-2"></i>
        @else
        <i class="fas fa-fw fa-circle mr-2"></i>
        @endif
        <span class="status__title">{{ $song->status->title }}</span>
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
        <x-delete-button :action="route( 'songs.destroy', ['song' => $song] )" :message="$song->attachments_count . ' attachments will also be deleted.'"/>
        @endcan
    </td>

</tr>