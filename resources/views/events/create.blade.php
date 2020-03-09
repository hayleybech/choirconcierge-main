@extends('layouts.app')

@section('title', 'Add Event')

@section('content')

    <h2 class="display-4 mb-4">Add Event</h2>

    @include('partials.flash')

    {{ Form::open( array( 'route' => 'events.index' ) ) }}

    <div class="form-group">
        {{ Form::label('title', 'Event Title') }}
        {{ Form::text('title', '', array('class' => 'form-control')) }}
    </div>

    <fieldset class="form-group">
        <legend class="col-form-label">Type</legend>
        @foreach($types as $type)
            <div class="custom-control custom-radio custom-control-inline">
                <input id="type_{{$type->id}}" name="type" value="{{$type->id}}" class="custom-control-input" type="radio">
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
            {{ Form::text('date_range', '', array('class' => 'form-control events-date-range-picker')) }}
            {{ Form::text('start_date', '', array('class' => 'start-date-hidden')) }}
            {{ Form::text('end_date', '', array('class' => 'end-date-hidden')) }}

        </div>
    </div>

    <div class="form-group">
        {{ Form::label('call_time', 'Call Time') }}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
            </div>
            {{ Form::text('call_time_input', '', array('class' => 'form-control events-single-date-picker')) }}
            {{ Form::text('call_time', '', array('class' => 'call-time-hidden')) }}
        </div>
    </div>

    <!--
    <div class="form-group">
        {{ Form::label('call_time_hr', 'Call Time') }}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-fw fa-clock"></i></span>
                <span class="input-group-text call-time-date">January 1, 2000</span>
            </div>
            {{ Form::select('call_time_hr', [
                    '01' => '01',
                    '02' => '02',
                    '03' => '03',
                    '04' => '04',
                    '05' => '05',
                    '06' => '06',
                    '07' => '07',
                    '08' => '08',
                    '09' => '09',
                    '10' => '10',
                    '11' => '11',
                    '12' => '12',
                ], '', ['class' => 'custom-select call-time-hr']) }}
            {{ Form::select('call_time_min', [
                    '00' => '00',
                    '15' => '15',
                    '30' => '30',
                    '45' => '45',
                ], '', ['class' => 'custom-select call-time-min']) }}
            {{ Form::select('call_time_ampm', [
                    'AM' => 'AM',
                    'PM' => 'PM'
                ], '', ['class' => 'custom-select time-ampm']) }}
            {{ Form::text('call_time', '', array('class' => 'call-time-hidden')) }}
        </div>
    </div>-->

    <div class="form-group">
        {{ Form::label('location', 'Location') }}
        {{ Form::textarea('location', '', array('class' => 'form-control', 'rows' => '3')) }}
    </div>

    <div class="form-group">
        {{ Form::label('description', 'Description') }}
        {{ Form::textarea('description', '', array('class' => 'form-control', 'rows' => '3')) }}
    </div>

    {{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

    @push('scripts-footer-bottom')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script src="{{ asset('js/events.js') }}"></script>
    @endpush

@endsection