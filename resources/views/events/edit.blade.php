@extends('layouts.app')

@section('title', 'Edit - ' . $event->title)

@section('content')

    <h2 class="display-4 mb-4">{{$event->title}}</h2>
    <h3>Edit Event</h3>

    @include('partials.flash')

    {{ Form::open( array( 'route' => ['events.show', $event->id], 'method' => 'put' ) ) }}

    <div class="form-group">
        {{ Form::label('title', 'Event Title') }}
        {{ Form::text('title', $event->title, array('class' => 'form-control')) }}
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
            {{ Form::text('date_range', $event->start_date->format('M d, Y H:i') . ' - ' . $event->end_date->format('M d, Y H:i'), array('class' => 'form-control events-date-range-picker')) }}
            {{ Form::hidden('start_date', $event->start_date, array('class' => 'start-date-hidden')) }}
            {{ Form::hidden('end_date', $event->end_date, array('class' => 'end-date-hidden')) }}

        </div>
    </div>

    <div class="form-group">
        {{ Form::label('call_time', 'Call Time') }}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
            </div>
            {{ Form::text('call_time_input', $event->call_time->format('M d, Y H:i'), array('class' => 'form-control events-single-date-picker')) }}
            {{ Form::hidden('call_time', $event->call_time, array('class' => 'call-time-hidden')) }}
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

    <div class="form-group location-input-wrapper">
        {{ Form::label('location', 'Location') }}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-fw fa-map-marker-alt"></i></span>
            </div>
            {{ Form::text('location', $event->location_name . ', ' . $event->location_address, array('class' => 'form-control location-input', 'rows' => '3')) }}
            {{ Form::hidden('location_place_id', $event->location_place_id, array('class' => 'form-control location-place-id')) }}
            {{ Form::hidden('location_icon', $event->location_icon, array('class' => 'form-control location-icon')) }}
            {{ Form::hidden('location_name', $event->location_name, array('class' => 'form-control location-name')) }}
            {{ Form::hidden('location_address', $event->location_address, array('class' => 'form-control location-address')) }}
        </div>
        <small class="location-place form-text text-muted">
            <span class="place-icon" style="background-image: url('{{ $event->location_icon }}');"></span>
            <span class="place-name">{{ $event->location_name }}</span>
            <span class="place-address">{{ $event->location_address }}</span>
        </small>
    </div>

    <div class="form-group">
        {{ Form::label('description', 'Description') }}
        {{ Form::textarea('description', $event->description, array('class' => 'form-control', 'rows' => '3')) }}
    </div>


    {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

    @push('scripts-footer-bottom')
        <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
        <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=<?= env('API_GOOGLE_KEY') ?>&libraries=places&callback=initMap" async defer></script>

        <script src="{{ asset('js/events.js') }}"></script>
    @endpush

@endsection