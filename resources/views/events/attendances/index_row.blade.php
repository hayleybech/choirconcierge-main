<tr class="row--singer">
    <td class="col--title">
        @can('view', $singer)
        <a href="{{route('singers.show', ['singer' => $singer])}}">
            <img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->name }}" class="user-avatar" width="50" height="50">
        </a>
        @else
        <img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->name }}" class="user-avatar" width="50" height="50">
        @endcan
        <div class="item-title-wrapper">
            @can('view', $singer)
            <a href="{{ route('singers.show', ['singer' => $singer]) }}">
                {{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
            </a>
            @else
            {{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
            @endcan
            <p>
                <span class="badge badge-pill badge-secondary" {!! ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? 'style="background-color: '.$singer->voice_part->colour.';"' : '' !!}>
			    <span class="d-md-none">{{ substr( $singer->voice_part->title ?? 'None', 0, 5 ) }}</span>
			    <span class="d-none d-md-inline">{{ ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? $singer->voice_part->title : 'No part' }}</span>
		</span></p>
        </div>
    </td>
    <td class="col--attendance">
        <label for="attendance_response_{{ $singer->id }}" class="d-block sr-only">Did they attend?</label>

        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-outline-success">
                <input type="radio" name="attendance_response[{{ $singer->id }}]" id="attendance_response_{{ $singer->id }}_present" value="present" {{ optional($singer->attendance)->response === 'present' ? 'checked' : '' }}> <i class="far fa-fw fa-check"></i><span class="d-none d-sm-inline"> Present</span>
            </label>
            <label class="btn btn-outline-danger">
                <input type="radio" name="attendance_response[{{ $singer->id }}]" id="attendance_response_{{ $singer->id }}_absent" value="absent" {{ optional($singer->attendance)->response === 'absent' ? 'checked' : '' }}> <i class="far fa-fw fa-times"></i><span class="d-none d-sm-inline"> Absent</span>
            </label>
            <label class="btn btn-outline-danger">
                <input type="radio" name="attendance_response[{{ $singer->id }}]" id="attendance_response_{{ $singer->id }}_absent_apology" value="absent_apology" {{ optional($singer->attendance)->response === 'absent_apology' ? 'checked' : '' }}> <i class="far fa-fw fa-times"></i> <span class="d-none d-sm-inline">With </span>Apology
            </label>
        </div>

    </td>
    <td class="col--attendance">
        <label for="absent_reason_{{ $singer->id }}" class="mr-2 sr-only">Reason</label>
        <input type="text" id="absent_reason_{{ $singer->id }}" name="absent_reason[{{ $singer->id }}]" value="{{ optional($singer->attendance)->absent_reason }}" class="form-control form-control-sm">
    </td>