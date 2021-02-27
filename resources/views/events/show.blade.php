@extends('layouts.page-blank')

@section('title', $event->title . ' - Events')

@section('page-content')

    <div class="row">

        <div class="col-md-7">

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h1 class="h2 mb-0">
                        {{ $event->title }}
                        @if( $event->is_repeating )
                            <i class="fal fa-fw fa-repeat" title="Repeating Event"></i>
                        @endif
                    </h1>
                    @can('update', $event)
                        @if($event->is_repeating)
                            <a href="#" data-toggle="modal" data-target="#repeatingEventEditModeModal" class="btn btn-add btn-sm btn-primary flex-shrink-0"><i class="fa fa-fw fa-edit"></i> Edit</a>
                        @else
                        <a href="{{route( 'events.edit', ['event' => $event] )}}" class="btn btn-add btn-sm btn-primary flex-shrink-0"><i class="fa fa-fw fa-edit"></i> Edit</a>
                        @endif
                    @endcan
                </div>
                <div class="card-body">
                    <div class="badge badge-pill badge-dark">{{ $event->type->title }}</div>
                    <div><time class="font-weight-bold">{{ $event->start_date->format('M d, H:i') }}</time> to <time class="font-weight-bold">{{ $event->end_date->format('M d, H:i') }}</time></div>
                    <div>Call Time: <time>{{ $event->call_time->format('M d, H:i') }}</time></div>
                    <div>{{ $event->description }}</div>

                    <h4 class="mt-2">My RSVP</h4>
                    @if($my_rsvp)
                        @if($event->in_future)
                            <inline-edit-field action="{{ route('events.rsvps.update', ['event' => $event, 'rsvp' => $my_rsvp]) }}" value="{{ $my_rsvp->response_string }}" csrf="{{ csrf_token() }}" edit-label="Change response">
                                <label for="rsvp_response" class="d-block">Will you attend?</label>

                                <div class="custom-control custom-radio custom-control-inline">
                                    <input id="rsvp_response_yes" name="rsvp_response" value="yes" class="custom-control-input" type="radio" {{ 'yes' === $my_rsvp->response ? 'checked' : '' }}>
                                    <label for="rsvp_response_yes" class="custom-control-label">Yes</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input id="rsvp_response_maybe" name="rsvp_response" value="maybe" class="custom-control-input" type="radio" {{ 'maybe' === $my_rsvp->response ? 'checked' : '' }}>
                                    <label for="rsvp_response_maybe" class="custom-control-label">Maybe</label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input id="rsvp_response_no" name="rsvp_response" value="no" class="custom-control-input" type="radio" {{ 'no' === $my_rsvp->response ? 'checked' : '' }}>
                                    <label for="rsvp_response_no" class="custom-control-label">No</label>
                                </div>
                            </inline-edit-field>
                        @else
                            {{ $my_rsvp->response_string }}
                        @endif
                    @elseif($event->in_future)
                        {{ Form::open(['route' => ['events.rsvps.store', $event->id]]) }}
                        <div class="form-group">
                            <label for="rsvp_response" class="d-block">Will you attend?</label>

                            <div class="custom-control custom-radio custom-control-inline">
                                <input id="rsvp_response_yes" name="rsvp_response" value="yes" class="custom-control-input" type="radio">
                                <label for="rsvp_response_yes" class="custom-control-label">Yes</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input id="rsvp_response_maybe" name="rsvp_response" value="maybe" class="custom-control-input" type="radio" checked>
                                <label for="rsvp_response_maybe" class="custom-control-label">Maybe</label>
                            </div>
                            <div class="custom-control custom-radio custom-control-inline">
                                <input id="rsvp_response_no" name="rsvp_response" value="no" class="custom-control-input" type="radio">
                                <label for="rsvp_response_no" class="custom-control-label">No</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-secondary"><i class="far fa-fw fa-check"></i> Save RSVP</button>
                        </div>
                        {{ Form::close() }}
                    @else
                        You didn't RSVP for this event.
                    @endif

                    @if( ! $event->in_future )
                    <h4 class="mt-2">My Attendance</h4>
                        @if($my_attendance)
                        <p>
                            {{ $my_attendance->response_string }}<br>
                            @if($my_attendance->absent_reason)
                            Reason: {{ $my_attendance->absent_reason }}
                            @endif
                        </p>
                        @else
                        <p>Not recorded.</p>
                        @endif
                    @endif
                </div>
            </div>

            <div class="card">
                <h3 class="card-header h4">Location</h3>

                <div class="card-body">
                    <p>
                        <span style="background-image: url('{{ $event->location_icon }}');" class="place-icon"></span> <span class="place-name">{{ $event->location_name }}</span> <br>
                        <span class="place-address">{{ $event->location_address }}</span>
                    </p>

                    <div class="event-map google-maps">
                        <iframe
                                width="600"
                                height="450"
                                frameborder="0" style="border:0"
                                src="https://www.google.com/maps/embed/v1/place?key=<?= config('services.google.key') ?>&q=place_id:<?= urlencode($event->location_place_id)?>"
                                allowfullscreen>
                        </iframe>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-md-5">

            @can ('viewAny', \App\Models\Rsvp::class)
            <div class="card">
                <div class="card-header">
                    <h4>RSVP Report</h4>
                </div>
                <div class="card-body">
                    <h5>Summary</h5>
                    <div class="row text-center mb-4">
                        <div class="col-6 col-md-3">
                            <strong>Going</strong><br>
                            {{ $singers_rsvp_yes_count }}
                        </div>
                        <div class="col-6 col-md-3">
                            <strong>Unknown</strong><br>
                            {{ $singers_rsvp_missing_count }}
                        </div>
                        <div class="col-6 col-md-3">
                            Maybe<br>
                            {{ $singers_rsvp_maybe_count }}
                        </div>
                        <div class="col-6 col-md-3">
                            Not going<br>
                            {{ $singers_rsvp_no_count }}
                        </div>
                    </div>
                    <h5>Voice Parts</h5>
                    <div class="row text-center mb-4">
                        @foreach($voice_parts_rsvp_yes_count as $voice_part)
                        <div class="col-6 col-md-3">
                            {{ $voice_part->title }}<br>
                            {{ $voice_part->response_count }}<br>
                            <small>confirmed</small>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endcan

            @can('viewAny', \App\Models\Attendance::class)
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <h4>Attendance Report</h4>
                    @can('create', \App\Models\Attendance::class)
                    <a href="{{ route('events.attendances.index', ['event' => $event]) }}" class="btn btn-secondary btn-sm"><i class="fas fa-fw fa-edit"></i> Record Attendance</a>
                    @endcan
                </div>
                <div class="card-body">
                    <h5>Summary</h5>
                    <div class="row text-center mb-4">
                        <div class="col-6 col-md-3">
                            <strong>Present</strong><br>
                            {{ $singers_attendance_present }}
                        </div>
                        <div class="col-6 col-md-3">
                            <strong>Absent</strong><br>
                            {{ $singers_attendance_absent }}
                        </div>
                        <div class="col-6 col-md-3">
                            With Apology<br>
                            {{ $singers_attendance_absent_apology }}
                        </div>
                        <div class="col-6 col-md-3">
                            Not recorded<br>
                            {{ $singers_attendance_missing }}
                        </div>
                    </div>
                    <h5>Voice Parts</h5>
                    <div class="row text-center mb-4">
                        @foreach($voice_parts_attendance as $voice_part)
                            <div class="col-6 col-md-3">
                                {{ $voice_part->title }}<br>
                                {{ $voice_part->response_count }}<br>
                                <small>present</small>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endcan
        </div>

    </div>

    @if($event->is_repeating)
    <repeating-event-edit-mode-modal
            route="{{ route('events.edit-recurring', ['event' => $event, 'mode' => '--replace--']) }}"
            :event-in-past="{{ json_encode($event->in_past, JSON_THROW_ON_ERROR) }}"
            :parent-in-past="{{ json_encode($parent_in_past, JSON_THROW_ON_ERROR) }}"
            :is-parent="{{ json_encode($event->is_repeat_parent), JSON_THROW_ON_ERROR) }}"
    ></repeating-event-edit-mode-modal>
    @endif

@endsection
<script>
    import InlineEditField from "../../assets/js/components/InlineEditField";
    export default {
        components: {InlineEditField}
    }
</script>