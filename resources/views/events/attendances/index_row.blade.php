<tr class="row--singer">
    <td class="col--title">
        @can('view', $attendance->singer)
        <a href="{{route('singers.show', ['singer' => $attendance->singer])}}">
            <img src="{{ $attendance->singer->user->avatar_url }}" alt="{{ $attendance->singer->user->name }}" class="user-avatar" width="50" height="50">
        </a>
        @else
        <img src="{{ $attendance->singer->user->avatar_url }}" alt="{{ $attendance->singer->user->name }}" class="user-avatar" width="50" height="50">
        @endcan
        <div class="item-title-wrapper">
            @can('view', $attendance->singer)
            <a href="{{ route('singers.show', ['singer' => $attendance->singer]) }}">
                {{ ( isset($attendance->singer->user->name) ) ? $attendance->singer->user->name : 'Name Unknown' }}
            </a>
            @else
            {{ ( isset($attendance->singer->user->name) ) ? $attendance->singer->user->name : 'Name Unknown' }}
            @endcan
            <div>
                <span class="badge badge-pill badge-light" {!! $voice_part->id ? 'style="background-color: '.$voice_part->colour.';"' : '' !!}>
                    <span class="d-md-none">{{ $voice_part->id ? $voice_part->title : 'None' }}</span>
                    <span class="d-none d-md-inline">{{ $voice_part->title }}</span>
		        </span>
            </div>
        </div>
    </td>
    <td class="col--attendance">
        <label for="attendance_response_{{ $attendance->singer->id }}" class="d-block sr-only">Did they attend?</label>

        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-outline-success">
                <input type="radio" name="attendance_response[{{ $attendance->singer->id }}]" id="attendance_response_{{ $attendance->singer->id }}_present" value="present" {{ $attendance->response === 'present' ? 'checked' : '' }}> <i class="far fa-fw fa-check"></i><span class="d-none d-sm-inline"> Present</span>
            </label>
            <label class="btn btn-outline-danger">
                <input type="radio" name="attendance_response[{{ $attendance->singer->id }}]" id="attendance_response_{{ $attendance->singer->id }}_absent" value="absent" {{ $attendance->response === 'absent' ? 'checked' : '' }}> <i class="far fa-fw fa-times"></i><span class="d-none d-sm-inline"> Absent</span>
            </label>
            <label class="btn btn-outline-danger">
                <input type="radio" name="attendance_response[{{ $attendance->singer->id }}]" id="attendance_response_{{ $attendance->singer->id }}_absent_apology" value="absent_apology" {{ $attendance->response === 'absent_apology' ? 'checked' : '' }}> <i class="far fa-fw fa-times"></i> <span class="d-none d-sm-inline">With </span>Apology
            </label>
        </div>

    </td>
    <td class="col--reason">
        <x-inputs.text label="Reason" id="absent_reason_{{ $attendance->singer->id }}" name="absent_reason[{{ $attendance->singer->id }}]" :value="$attendance->absent_reason" small="true" label-class="mr-2 d-sm-none"></x-inputs.text>
    </td>