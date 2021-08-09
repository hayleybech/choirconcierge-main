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

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            @if($event->call_time->isSameDay($event->end_date))
                                <div class="h3">
                                    <!-- Single Date -->
                                    <time>{{ $event->call_time->format(config('app.formats.date_lg')) }}</time>
                                </div>
                                <div class="h5">
                                    <!-- Time -->
                                    <time>{{ $event->call_time->format(config('app.formats.time')) }}</time> to <time>{{ $event->end_date->format(config('app.formats.time')) }}</time>
                                </div>
                            @else
                                <div class="h3">
                                    <!-- Date Range -->
                                    <time>{{ $event->call_time->format(config('app.formats.date_md')) }}</time> to <time>{{ $event->end_date->format(config('app.formats.date_lg')) }}</time>
                                </div>
                                <div class="h5">
                                    <!-- Time -->
                                    <time>{{ $event->call_time->format(config('app.formats.timestamp_md')) }}</time> to <time>{{ $event->end_date->format(config('app.formats.timestamp_md')) }}</time>
                                </div>
                            @endif

                            <small class="text-muted">Timezone: {{ $event->start_date->format(config('app.formats.timezone')) }}</small>
                        </div>
                        <div class="col-md-6 mb-4">
                            <h5>Event Details</h5>
                            <dl class="row mx-n1">
                                <dt class="col-sm-4 px-1">Category:</dt>
                                <dd class="col-sm-8 px-1"><span class="badge badge-pill badge-dark">{{ $event->type->title }}</span></dd>

                                @can('update', $event)
                                <dt class="col-sm-4 px-1">On stage:</dt>
                                <dd class="col-sm-8 px-1"><time>{{ $event->start_date->format(config('app.formats.time')) }}</time></dd>
                                @endcan

                                @if($event->is_repeating)
                                <dt class="col-sm-4 px-1">Repeat:</dt>
                                <dd class="col-sm-8 px-1">Every {{ $event->repeat_frequency_unit }} until <time>{{ $event->repeat_until->format(config('app.formats.date_sm')) }}</time></dd>
                                @endif
                            </dl>
                        </div>
                    </div>

                    <h5>Event Description</h5>
                    <div class="mb-4">
                        <read-more more-str="Read More" text="{{ $event->description }}" less-str="Read Less" :max-chars="500"></read-more>
                    </div>

                    <div class="dropup">
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

            <div class="card">
                <div class="card-header"><h4>My Attendance</h4></div>
                <div class="card-body">
                    <!-- RSVP -->
                    <x-inline-rsvp :event="$event"></x-inline-rsvp>

                    @if( ! $event->in_future )
                        <!-- Attendance -->
                        @if($my_attendance)
                            <p>
                                You were {{ strtolower($my_attendance->response_string) }} for this event.<br>
                                @if($my_attendance->absent_reason)
                                    Reason: {{ $my_attendance->absent_reason }}
                                @endif
                            </p>
                        @else
                            <p>Your attendance has not been recorded for this event.</p>
                        @endif
                    @endif
                </div>
            </div>

            @can ('viewAny', \App\Models\Rsvp::class)
            <div class="card">

                <div class="card-tabs nav nav-tabs">
                    <a href="#pane-rsvp-response" class="card-tab nav-link active" id="tab-rsvp-response" data-toggle="tab">By Response</a>
                    <a href="#pane-rsvp-part" class="card-tab nav-link" id="tab-rsvp-part" data-toggle="tab">By Voice Part</a>
                </div>

                <div class="tab-content">

                    <div class="tab-pane active" id="pane-rsvp-response" role="tabpanel" aria-labelledby="tab-rsvp-response">
                        <div class="card-body">
                            <h4>RSVP Summary</h4>

                            <div class="row text-center mb-4">
                                <div class="col-6 col-md-4">
                                    <strong class="text-success">
                                        <i class="fas fa-fw fa-check"></i><br>
                                        Going
                                    </strong>
                                    <br>
                                    {{ $singers_rsvp_yes_count }}
                                </div>
                                <div class="col-6 col-md-4">
                                    <strong class="text-warning">
                                        <i class="fas fa-fw fa-question"></i><br>
                                        Unknown
                                    </strong>
                                    <br>
                                    {{ $singers_rsvp_missing_count }}
                                </div>
                                <div class="col-6 col-md-4">
                                    <strong class="text-danger">
                                        <i class="fas fa-fw fa-times"></i><br>
                                        Not going
                                    </strong>
                                    <br>
                                    {{ $singers_rsvp_no_count }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="pane-rsvp-part" role="tabpanel" aria-labelledby="tab-rsvp-part">
                        <div class="card-body">
                            <h4>RSVP Summary</h4>

                            <div class="row text-center mb-4">
                                @foreach($voice_parts_rsvp_yes_count as $voice_part)
                                    <div class="col-6 col-md-3">
                                        {{ $voice_part->title }}<br>
                                        {{ $voice_part->singers_count }}<br>
                                        <small>confirmed</small>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endcan

            @can('viewAny', \App\Models\Attendance::class)
            <div class="card">
                <div class="card-tabs nav nav-tabs">
                    <a href="#pane-attendance-response" class="card-tab nav-link active" id="tab-attendance-response" data-toggle="tab">By Response</a>
                    <a href="#pane-attendance-part" class="card-tab nav-link" id="tab-attendance-part" data-toggle="tab">By Voice Part</a>
                </div>

                <div class="tab-content">

                    <div class="tab-pane active" id="pane-attendance-response" role="tabpanel" aria-labelledby="tab-attendance-response">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                                <h4>Attendance Summary</h4>
                                @can('create', \App\Models\Attendance::class)
                                    <a href="{{ route('events.attendances.index', ['event' => $event]) }}" class="btn btn-secondary btn-sm"><i class="fas fa-fw fa-edit"></i> Record Attendance</a>
                                @endcan
                            </div>

                            <div class="row text-center mb-4">
                                <div class="col-6 col-md-4">
                                    <strong class="text-success">
                                        <i class="fas fa-fw fa-check"></i>
                                        <br>
                                        Present
                                    </strong><br>
                                    {{ $singers_attendance_present }}
                                </div>
                                <div class="col-6 col-md-4">
                                    <strong class="text-warning">
                                        <i class="fas fa-fw fa-question"></i>
                                        <br>
                                        Not recorded
                                    </strong>
                                    <br>
                                    {{ $singers_attendance_missing }}
                                </div>
                                <div class="col-6 col-md-4">
                                    <strong class="text-danger">
                                        <i class="fas fa-fw fa-times"></i>
                                        <br>
                                        Absent
                                    </strong>
                                    <br>
                                    {{ $singers_attendance_absent + $singers_attendance_absent_apology }}<br>
                                    <small class="text-muted">({{ $singers_attendance_absent_apology }} With Apology)</small><br>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane" id="pane-attendance-part" role="tabpanel" aria-labelledby="tab-attendance-part">
                        <div class="card-body">
                            <div class="d-flex flex-wrap justify-content-between align-items-start mb-3">
                                <h4>Attendance Summary</h4>
                                @can('create', \App\Models\Attendance::class)
                                    <a href="{{ route('events.attendances.index', ['event' => $event]) }}" class="btn btn-secondary btn-sm"><i class="fas fa-fw fa-edit"></i> Record Attendance</a>
                                @endcan
                            </div>

                            <div class="row text-center mb-4">
                                @foreach($voice_parts_attendance as $voice_part)
                                    <div class="col-6 col-md-3">
                                        {{ $voice_part->title }}<br>
                                        {{ $voice_part->singers_count }}<br>
                                        <small>present</small>
                                    </div>
                                @endforeach
                            </div>

                        </div>
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