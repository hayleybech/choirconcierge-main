<tr class="row--singer">
    <td class="col--title">
        <div class="d-flex align-items-center">
            @can('view', $singer)
                <a href="{{route('singers.show', ['singer' => $singer])}}">
                    <img src="{{ $singer->user_avatar_thumb_url }}" alt="{{ $singer->user->name }}" class="user-avatar" width="30" height="30">
                </a>
            @else
                <img src="{{ $singer->user_avatar_thumb_url }}" alt="{{ $singer->user->name }}" class="user-avatar" width="30" height="30">
            @endcan
            <div class="item-title-wrapper">
                @can('view', $singer)
                    <a href="{{ route('singers.show', ['singer' => $singer]) }}">
                        {{ ( isset($singer->user->name) ) ? $singer->user->name : 'Name Unknown' }}
                    </a>
            @else
                {{ ( isset($singer->user->name) ) ? $singer->user->name : 'Name Unknown' }}
            @endcan
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
        <small>
            {{ floor( $singer->attendances->where('response', 'present')->count() / $events->count() * 100 ) }}%&nbsp;({{ $singer->attendances->where('response', 'present')->count() }}/{{ $events->count() }})
        </small>
    </td>
</tr>