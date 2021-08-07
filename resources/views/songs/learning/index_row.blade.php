<tr class="row--singer">
    <td class="col--title">
        @can('view', $singer)
        <a href="{{route('singers.show', ['singer' => $singer])}}">
            <img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->user->name }}" class="user-avatar" width="50" height="50">
        </a>
        @else
        <img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->user->name }}" class="user-avatar" width="50" height="50">
        @endcan
        <div class="item-title-wrapper">
            @can('view', $singer)
            <a href="{{ route('singers.show', ['singer' => $singer]) }}">
                {{ ( isset($singer->user->name) ) ? $singer->user->name : 'Name Unknown' }}
            </a>
            @else
            {{ ( isset($singer->user->name) ) ? $singer->user->name : 'Name Unknown' }}
            @endcan
            <div>
                <span class="badge badge-pill badge-light" {!! ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? 'style="background-color: '.$singer->voice_part->colour.';"' : '' !!}>
                    <span class="d-md-none">{{ substr( $singer->voice_part->title ?? 'None', 0, 5 ) }}</span>
                    <span class="d-none d-md-inline">{{ ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? $singer->voice_part->title : 'No part' }}</span>
		        </span>
            </div>
        </div>
    </td>
    <td class="col--attendance">
        <span class="mr-2 font-weight-bold text-{{ $singer->learning->status_colour }}">
            @if($singer->learning->status === 'performance-ready')
            <i class="fas fa-fw fa-check-double mr-2"></i>
            @elseif($singer->learning->status === 'assessment-ready')
                <i class="fas fa-fw fa-check mr-2"></i>
            @else
                <i class="fas fa-fw fa-clock mr-2"></i>
            @endif
            {{ $singer->learning->status_name }}
        </span>
    </td>
    <td class="col--reason">
        @if($singer->learning->status !== 'performance-ready')
        <form action="{{ route('songs.singers.update', [$singer->learning->song_id, $singer]) }}" method="post" class="d-inline-block">
            @csrf
            @method('put')
            <input type="hidden" name="status" value="performance-ready" />
            <button type="submit" class="btn btn-success btn-sm mr-2"><i class="far fa-fw fa-check-double"></i> Performance Ready</button>
                </form>
        @endif
        @if($singer->learning->status !== 'not-started')
        <form action="{{ route('songs.singers.update', [$singer->learning->song_id, $singer]) }}" method="post" class="d-inline-block">
            @csrf
            @method('put')
            <input type="hidden" name="status" value="not-started" />
            <button type="submit" class="btn btn-danger btn-sm mr-2"><i class="far fa-fw fa-clock"></i> Learning</button>
        </form>
        @endif
    </td>
</tr>