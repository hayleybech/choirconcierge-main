@props(['event', 'myRsvp', 'compact' => false])
@php
    use App\Models\Event
    /**
     * @var Event $event
     **/
@endphp
@if($event->my_rsvp)
    @if(! $compact)
    Your RSVP response:
    @endif
    @if($event->in_future)
        <!-- Edit RSVP -->
        <inline-edit-field action="{{ route('events.rsvps.update', ['event' => $event, 'rsvp' => $event->my_rsvp]) }}" value="{{ $event->my_rsvp->response_label }}" csrf="{{ csrf_token() }}" edit-label="Change response" :small-buttons="true">
            @if(! $compact)
            <label for="rsvp_response" class="d-block">Will you attend?</label>
            @endif

            <div class="btn-group btn-group-sm btn-group-toggle mr-2" data-toggle="buttons">
                <label class="btn btn-outline-success">
                    <input type="radio" name="rsvp_response" id="rsvp_response_yes" value="yes" {{'yes' === $event->my_rsvp->response ? 'checked' : ''}}> <i class="far fa-fw fa-check"></i><span class="d-none d-sm-inline"> Going</span>
                </label>
                <label class="btn btn-outline-warning">
                    <input type="radio" name="rsvp_response" id="rsvp_response_maybe" value="maybe" {{'maybe' === $event->my_rsvp->response ? 'checked' : ''}}> <i class="far fa-fw fa-question"></i><span class="d-none d-sm-inline"> Maybe</span>
                </label>
                <label class="btn btn-outline-danger">
                    <input type="radio" name="rsvp_response" id="rsvp_response_no" value="no" {{'no' === $event->my_rsvp->response ? 'checked' : ''}}> <i class="far fa-fw fa-times"></i> <span class="d-none d-sm-inline">Not Going</span>
                </label>
            </div>
        </inline-edit-field>
    @else
        {{ $event->my_rsvp->response_label }}
    @endif
@elseif($event->in_future)
    <!-- Create RSVP -->
    {{ Form::open(['route' => ['events.rsvps.store', $event->id]]) }}
    <div class="mt-2">
        @if(! $compact)
        <label for="rsvp_response" class="d-block">Will you attend?</label>
        @endif

        <div class="btn-group btn-group-sm btn-group-toggle mr-2" data-toggle="buttons">
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

        <button type="submit" class="btn btn-secondary btn-sm"><i class="far fa-fw fa-check"></i> Save</button>
    </div>

    {{ Form::close() }}
@else
    You didn't RSVP for this event.
@endif