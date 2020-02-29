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
        {{ Form::text('start_date', $event->start_date, array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
        {{ Form::label('location', 'Location') }}
        {{ Form::text('location', $event->location, array('class' => 'form-control')) }}
    </div>


    {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

@endsection