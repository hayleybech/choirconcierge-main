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
        {{ Form::label('start_date', 'Start Date') }}
        <div class="input-group">
            <div class="input-group-prepend">
                <span class="input-group-text"><i class="fa fa-fw fa-calendar"></i></span>
            </div>
            {{ Form::text('start_date', $event->start_date, array('class' => 'form-control events-single-date-picker')) }}
        </div>
    </div>

    <div class="form-group">
        {{ Form::label('location', 'Location') }}
        {{ Form::text('location', $event->location, array('class' => 'form-control')) }}
    </div>


    {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

    @push('scripts-footer-bottom')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    <script type="text/javascript">
        // Events - Single Date
        $('.events-single-date-picker').daterangepicker({
            "singleDatePicker": true,
            "showISOWeekNumbers": true,
            "timePicker": true,
            "locale": {
                "format": "YYYY-MM-DD HH:mm",
                "firstDay": 1
            }
        }, function(start, end, label) {
            console.log('New date range selected: ' + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD') + ' (predefined range: ' + label + ')');
        });
    </script>
    @endpush

@endsection