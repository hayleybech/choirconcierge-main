@extends('layouts.app')

@section('title', 'Add Song')

@section('content')

    <h2>Add Song</h2>

    @include('partials.flash')

    {{ Form::open( array( 'route' => 'songs.index' ) ) }}

    <div class="form-group">
        {{ Form::label('title', 'Song Title') }}
        {{ Form::text('title', '', array('class' => 'form-control')) }}
    </div>

    <fieldset class="form-group">
        <legend class="col-form-label">Category</legend>
        @foreach($categories as $cat)
            <div class="custom-control custom-checkbox custom-control-inline">
                <input id="categories_{{$cat->id}}" name="categories[]" value="{{$cat->id}}" class="custom-control-input" type="checkbox">
                <label for="categories_{{$cat->id}}" class="custom-control-label">{{$cat->title}}</label>
            </div>
        @endforeach
    </fieldset>


    <fieldset class="form-group">
        <legend class="col-form-label">Status</legend>
        @foreach($statuses as $status)
            <div class="custom-control custom-radio custom-control-inline">
                <input id="status_{{$status->id}}" name="status" value="{{$status->id}}" class="custom-control-input" type="radio">
                <label for="status_{{$status->id}}" class="custom-control-label">{{$status->title}}</label>
            </div>
        @endforeach
    </fieldset>

    <div class="form-group">
        {{ Form::label('pitch_blown', 'Pitch Blown') }}
        {{ Form::select('pitch_blown',
            $pitches,
            '',
            ['class' => 'custom-select']
        ) }}
    </div>

    {{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

@endsection