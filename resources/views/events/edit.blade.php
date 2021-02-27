<?php /** @var App\Models\Event $event */ ?>
@extends('layouts.page')

@section('title', 'Edit - ' . $event->title)
@section('page-title')
    {{ $event->title }}
    @if( $event->is_repeating )
        <i class="fal fa-fw fa-repeat" title="Repeating Event"></i>
    @endif
@endsection

@section('page-content')

    {{ Form::open( [ 'route' => ['events.show', $event->id], 'method' => 'put' ] ) }}

    <input type="hidden" name="edit_mode" value="{{ request()->query('mode') }}">

    @if($event->is_repeating)
        <div class="alert alert-warning  py-3 px-3 text-left d-flex align-items-center">
            @if('single' === request()->query('mode'))
            <i class="far fa-fw fa-calendar-day fa-2x mr-3"></i>
            <span>
                <span class="h5">Editing only this event</span>
                <span class="form-text">
                    All other events in the series will remain the same.
                </span>
            </span>
            @elseif('following' === request()->query('mode'))
                <i class="far fa-fw fa-calendar-week fa-2x mr-3"></i>
                <span>
                    <span class="h5">Editing following events</span>
                    <span class="form-text">
                        This and all the following events will be changed.<br>
                        <strong>Any changes to future events will be lost, including RSVPs.</strong>
                    </span>
                </span>
            @elseif('all' === request()->query('mode'))
            <i class="far fa-fw fa-calendar-alt fa-2x mr-3"></i>
            <span>
                <span class="h5">Editing all events</span>
                <span class="form-text">
                    All events in the series will be changed.<br>
                    <strong>Any changes to other events will be lost, including RSVPs and attendance records.</strong>
                </span>
            </span>
            @endif
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h3 class="card-header h4">Edit Event</h3>

                <div class="card-body">
                    <div class="form-group">
                        {{ Form::label('title', 'Event Title') }}
                        {{ Form::text('title', $event->title, ['class' => 'form-control']) }}
                    </div>

                    <fieldset class="form-group">
                        <legend class="col-form-label">Type</legend>
                        @foreach($types as $type)
                            <div class="custom-control custom-radio custom-control-inline">
                                <input id="type_{{$type->id}}" name="type" value="{{$type->id}}" class="custom-control-input" type="radio" {{ ($event->type->id === $type->id ) ? 'checked' : '' }}>
                                <label for="type_{{$type->id}}" class="custom-control-label">{{$type->title}}</label>
                            </div>
                        @endforeach
                    </fieldset>

                    <div class="form-group">
                        {{ Form::label('date_range', 'Event Date') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-calendar-day"></i></span>
                            </div>
                            {{ Form::text('date_range', $event->start_date->format('M d, Y H:i') . ' - ' . $event->end_date->format('M d, Y H:i'), ['class' => 'form-control events-date-range-picker']) }}
                            {{ Form::hidden('start_date', $event->start_date, ['class' => 'start-date-hidden']) }}
                            {{ Form::hidden('end_date', $event->end_date, ['class' => 'end-date-hidden']) }}

                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('call_time', 'Call Time') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                            </div>
                            {{ Form::text('call_time_input', $event->call_time->format('M d, Y H:i'), ['class' => 'form-control events-single-date-time-picker']) }}
                            {{ Form::hidden('call_time', $event->call_time, ['class' => 'date-time-hidden']) }}
                        </div>
                    </div>

                <!--
    <div class="form-group">
        {{ Form::label('call_time_hr', 'Call Time') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                            </div>
{{ Form::select('call_time_hr', [
                    '01',
                    '02',
                    '03',
                    '04',
                    '05',
                    '06',
                    '07',
                    '08',
                    '09',
                    '10',
                    '11',
                    '12',
                ], $event->call_time_hr, ['class' => 'custom-select time-hr']) }}
                {{ Form::select('call_time_min', [
                        '00',
                        '15',
                        '30',
                        '45',
                    ], $event->call_time_min, ['class' => 'custom-select time-min']) }}
                {{ Form::select('call_time_ampm', [
                        'AM',
                        'PM'
                    ], $event->call_time_ampm, ['class' => 'custom-select time-ampm']) }}
                        </div>
                    </div>-->

                    <div class="form-group">
                        {{ Form::label('', 'Repeating Event') }}
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="is_repeating" name="is_repeating" value="1" {{ $event->is_repeating ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_repeating">Repeat?</label>
                        </div>
                    </div>

                    <fieldset id="repeat_details" style="padding: 15px; border: 1px solid rgb(221, 221, 221); border-radius: 10px; margin-bottom: 10px;">

                        <div class="form-group">
                            {{ Form::label('repeat_frequency_unit', 'Repeat every') }}<br>

                            @foreach(['day' => 'Day', 'week' => 'Week', 'month' => 'Month', 'year' => 'Year'] as $key => $unit)
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input id="repeat_frequency_unit_{{$key}}" name="repeat_frequency_unit" value="{{$key}}" class="custom-control-input" type="radio" {{ ($event->repeat_frequency_unit === $key ) ? 'checked' : '' }}>
                                    <label for="repeat_frequency_unit_{{$key}}" class="custom-control-label">{{$unit}}</label>
                                </div>
                            @endforeach

                        </div>

                        <div class="form-group">
                            {{ Form::label('repeat_until', 'Repeat until') }}
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fa fa-fw fa-calendar-day"></i></span>
                                </div>
                                {{ Form::text('repeat_until_input', $event->repeat_until->format('M d, Y H:i'), ['class' => 'form-control events-single-date-picker']) }}
                                {{ Form::hidden('repeat_until', $event->repeat_until, ['class' => 'date-time-hidden']) }}
                            </div>
                        </div>

                    </fieldset>

                    <div class="form-group location-input-wrapper">
                        {{ Form::label('location', 'Location') }}
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fa fa-fw fa-map-marker-alt"></i></span>
                            </div>
                            {{ Form::text('location', $event->location_name . ', ' . $event->location_address, ['class' => 'form-control location-input', 'rows' => '3']) }}
                            {{ Form::hidden('location_place_id', $event->location_place_id, ['class' => 'form-control location-place-id']) }}
                            {{ Form::hidden('location_icon', $event->location_icon, ['class' => 'form-control location-icon']) }}
                            {{ Form::hidden('location_name', $event->location_name, ['class' => 'form-control location-name']) }}
                            {{ Form::hidden('location_address', $event->location_address, ['class' => 'form-control location-address']) }}
                        </div>
                        <small class="location-place form-text text-muted">
                            <span class="place-icon" style="background-image: url('{{ $event->location_icon }}');"></span>
                            <span class="place-name">{{ $event->location_name }}</span>
                            <span class="place-address">{{ $event->location_address }}</span>
                        </small>
                    </div>

                    <div class="form-group">
                        {{ Form::label('description', 'Description') }}
                        <limited-textarea field-id="description" field-name="description" value="{{ $event->description }}" :maxlength="5000"></limited-textarea>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Save
                    </button>
                    <a href="{{ route('events.show', [$event]) }}" class="btn btn-link text-danger">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>

            </div>

        </div>
    </div>

    {{ Form::close() }}

    @push('scripts-footer-bottom')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= config('services.google.key') ?>&libraries=places&callback=initMap" async defer></script>

        <script src="{{ global_asset('js/events.js') }}"></script>
    @endpush

@endsection