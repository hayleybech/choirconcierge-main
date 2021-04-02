@props(['event', 'myRsvp'])
@php
    use App\Models\Rsvp;
    use App\Models\Event
    /**
     * @var Event $event
     * @var Rsvp $myRsvp
     **/
@endphp
@if($myRsvp)
    Your RSVP response:
    @if($event->in_future)
        <!-- Edit RSVP -->
        <inline-edit-field action="{{ route('events.rsvps.update', ['event' => $event, 'rsvp' => $myRsvp]) }}" value="{{ $myRsvp->response_string }}" csrf="{{ csrf_token() }}" edit-label="Change response" :small-buttons="true">
            <label for="rsvp_response" class="d-block">Will you attend?</label>

            <x-inputs.radio label="Yes" id="rsvp_response_yes" name="rsvp_response" value="yes" inline="true" :checked="'yes' === $myRsvp->response"></x-inputs.radio>
            <x-inputs.radio label="Maybe" id="rsvp_response_maybe" name="rsvp_response" value="maybe" inline="true" :checked="'maybe' === $myRsvp->response"></x-inputs.radio>
            <x-inputs.radio label="No" id="rsvp_response_no" name="rsvp_response" value="no" inline="true" :checked="'no' === $myRsvp->response"></x-inputs.radio>
        </inline-edit-field>
    @else
        {{ $myRsvp->response_string }}
    @endif
@elseif($event->in_future)
    <!-- Create RSVP -->
    {{ Form::open(['route' => ['events.rsvps.store', $event->id]]) }}
    <div class="form-group">
        <label for="rsvp_response" class="d-block">Will you attend?</label>

        <x-inputs.radio label="Yes" id="rsvp_response_yes" name="rsvp_response" value="yes" inline="true"></x-inputs.radio>
        <x-inputs.radio label="Maybe" id="rsvp_response_maybe" name="rsvp_response" value="maybe" inline="true" checked="true"></x-inputs.radio>
        <x-inputs.radio label="No" id="rsvp_response_no" name="rsvp_response" value="no" inline="true"></x-inputs.radio>

        <div class="btn-group btn-group-sm btn-group-toggle" data-toggle="buttons">
            <label class="btn btn-outline-success">
                <input type="radio" name="rsvp_response" id="rsvp_response_yes" value="yes"> <i class="far fa-fw fa-check"></i><span class="d-none d-sm-inline"> Going</span>
            </label>
            <label class="btn btn-outline-warning">
                <input type="radio" name="rsvp_response" id="rsvp_response_maybe" value="maybe" checked> <i class="far fa-fw fa-question"></i><span class="d-none d-sm-inline"> Maybe</span>
            </label>
            <label class="btn btn-outline-danger">
                <input type="radio" name="rsvp_response" id="rsvp_response_no" value="no"> <i class="far fa-fw fa-times"></i> <span class="d-none d-sm-inline">Not Going</span>
            </label>
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-secondary btn-sm"><i class="far fa-fw fa-check"></i> Save RSVP</button>
    </div>
    {{ Form::close() }}
@else
    You didn't RSVP for this event.
@endif