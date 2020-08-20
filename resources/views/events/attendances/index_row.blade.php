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

        <div class="btn-group btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-outline-success">
                <input type="radio" name="attendance_response[{{ $singer->id }}]" id="attendance_response_{{ $singer->id }}_present" value="present"> <i class="far fa-fw fa-check"></i> Present
            </label>
            <label class="btn btn-outline-danger">
                <input type="radio" name="attendance_response[{{ $singer->id }}]" id="attendance_response_{{ $singer->id }}_absent" value="absent"> <i class="far fa-fw fa-times"></i> Absent
            </label>
        </div>

        <!--
        <div class="custom-control custom-radio custom-control-inline">
            <input id="attendance_response_yes" name="attendance_response" value="yes" class="custom-control-input" type="radio">
            <label for="attendance_response_yes" class="custom-control-label">Yes</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
            <input id="attendance_response_no" name="attendance_response" value="no" class="custom-control-input" type="radio">
            <label for="attendance_response_no" class="custom-control-label">No</label>
        </div>-->
    </td>