<tr class="row--singer">
    <td class="col--title">
        <div class="d-flex align-items-center">
            @can('view', $singer)
                <a href="{{route('singers.show', ['singer' => $singer])}}">
                    <img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->name }}" class="user-avatar" width="30" height="30">
                </a>
            @else
                <img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->name }}" class="user-avatar" width="30" height="30">
            @endcan
            <div class="item-title-wrapper">
                @can('view', $singer)
                    <a href="{{ route('singers.show', ['singer' => $singer]) }}">
                        {{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
                    </a>
            @else
                {{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
            @endcan
            <!--
            <div>
                <span class="badge badge-pill badge-secondary" {!! ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? 'style="background-color: '.$singer->voice_part->colour.';"' : '' !!}>
                    <span class="d-md-none">{{ substr( $singer->voice_part->title ?? 'None', 0, 5 ) }}</span>
                    <span class="d-none d-md-inline">{{ ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? $singer->voice_part->title : 'No part' }}</span>
		        </span>
            </div>
            -->
            </div>
        </div>
    </td>

    @foreach($events as $event)
    <td class="col--response">
        @php( $attendance = optional($singer->attendances)->where('event_id', $event->id)->first() )
        @if( $attendance )
            @if( $attendance->response === 'present' )
                <i class="fa fa-fw fa-check text-success"></i>
            @elseif($attendance->response === 'absent')
                <i class="fa fa-fw fa-times text-danger"></i>
            @else
                <i class="fa fa-fw fa-times text-danger"></i><strong class="text-danger">A</strong>
            @endif
        @else
            <small><i class="fa fa-fw fa-question text-secondary"></i></small>
        @endif
    </td>
    @endforeach

    <td class="col--response">
        <small>{{ $singer->attendances->where('response', 'present')->count() }}/{{ $events->count() }}</small>
    </td>
</tr>