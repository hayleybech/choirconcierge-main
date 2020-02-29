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
        {{ Form::label('start_date', 'Start Date') }}
        {{ Form::text('start_date', '', array('class' => 'form-control')) }}
    </div>

    <div class="form-group">
        {{ Form::label('location', 'Location') }}
        {{ Form::text('location', '', array('class' => 'form-control')) }}
    </div>

    {{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

@endsection