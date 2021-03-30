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
                    <p>
                        <div class="dropdown">
                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="addToCalendarDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="far fa-fw fa-calendar-plus"></i> Add to Calendar
                            </button>
                            <div class="dropdown-menu" aria-labelledby="addToCalendarDropdownButton">
                                <a class="dropdown-item" href="{{ $event->add_to_calendar_link->google() }}" target="_blank"><i class="fab fa-fw fa-google"></i> Google</a>
                                <a class="dropdown-item" href="{{ $event->add_to_calendar_link->yahoo() }}" target="_blank"><i class="fab fa-fw fa-yahoo"></i> Yahoo</a>
                                <a class="dropdown-item" href="{{ $event->add_to_calendar_link->webOutlook() }}" target="_blank"><i class="fab fa-fw fa-microsoft"></i> Outlook Web</a>
                                <a class="dropdown-item" href="{{ $event->add_to_calendar_link->ics() }}" target="_blank"><i class="fas fa-fw fa-download"></i> ICS (iCal, Outlook etc)</a>
                            </div>
                        </div>
                    </p>

                    <div class="badge badge-pill badge-dark">{{ $event->type->title }}</div>
                    <div><time class="font-weight-bold">{{ $event->call_time->format('M d, H:i') }}</time> to <time class="font-weight-bold">{{ $event->end_date->format('M d, H:i') }}</time></div>
                    @can('update', $event)
                        <div>Onstage Time: <time>{{ $event->start_date->format('H:i') }}</time></div>
                    @endcan
                    <div class="text-muted">Timezone: {{ $event->start_date->format('e P') }}</div>
                    <div>{{ $event->description }}</div>

                    <h4 class="mt-2">My RSVP</h4>
                    @if($my_rsvp)
                        @if($event->in_future)
                            <inline-edit-field action="{{ route('events.rsvps.update', ['event' => $event, 'rsvp' => $my_rsvp]) }}" value="{{ $my_rsvp->response_string }}" csrf="{{ csrf_token() }}" edit-label="Change response">
                                <label for="rsvp_response" class="d-block">Will you attend?</label>
                                
                                <x-inputs.radio label="Yes" id="rsvp_response_yes" name="rsvp_response" value="yes" inline="true" :checked="'yes' === $my_rsvp->response"></x-inputs.radio>
                                <x-inputs.radio label="Maybe" id="rsvp_response_maybe" name="rsvp_response" value="maybe" inline="true" :checked="'maybe' === $my_rsvp->response"></x-inputs.radio>
                                <x-inputs.radio label="No" id="rsvp_response_no" name="rsvp_response" value="no" inline="true" :checked="'no' === $my_rsvp->response"></x-inputs.radio>
                            </inline-edit-field>
                        @else
                            {{ $my_rsvp->response_string }}
                        @endif
                    @elseif($event->in_future)
                        {{ Form::open(['route' => ['events.rsvps.store', $event->id]]) }}
                        <div class="form-group">
                            <label for="rsvp_response" class="d-block">Will you attend?</label>

                            <x-inputs.radio label="Yes" id="rsvp_response_yes" name="rsvp_response" value="yes" inline="true"></x-inputs.radio>
                            <x-inputs.radio label="Maybe" id="rsvp_response_maybe" name="rsvp_response" value="maybe" inline="true" checked="true"></x-inputs.radio>
                            <x-inputs.radio label="No" id="rsvp_response_no" name="rsvp_response" value="no" inline="true"></x-inputs.radio>
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

                    <google-map api-key="{{ config('services.google.key')  }}" place-id="{{ urlencode($event->location_place_id) }}"></google-map>
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

    <p><small class="text-muted">Choir's Timezone: {{ tenant('timezone')->toRegionName() }} {{ tenant('timezone')->toOffsetName() }}</small></p>

    @if($event->is_repeating)
    <repeating-event-edit-mode-modal
            route="{{ route('events.edit-recurring', ['event' => $event, 'mode' => '--replace--']) }}"
            :event-in-past="{{ json_encode($event->in_past, JSON_THROW_ON_ERROR) }}"
            :parent-in-past="{{ json_encode(optional($event->repeat_parent)->in_past ?? null, JSON_THROW_ON_ERROR) }}"
            :is-parent="{{ json_encode($event->is_repeat_parent, JSON_THROW_ON_ERROR) }}"
    ></repeating-event-edit-mode-modal>
    @endif

@endsection