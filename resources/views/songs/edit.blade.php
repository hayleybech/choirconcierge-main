@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

    <h2>Add Song</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{ Form::open( array( 'route' => 'singers.index' ) ) }}

    <p>
        {{ Form::label('title', 'Song Title') }}
        {{ Form::text('title', '', array('class' => 'form-control')) }}
    </p>

    <p>
        {{ Form::label('category', 'Category') }}
        {{ Form::select('attachment-category', array('General', 'Australiana', 'Christmas'), '', ['class' => 'custom-select form-control-sm']) }}
    </p>
    <p>
        {{ Form::label('status', 'Status') }}
        {{ Form::select('status', array('Pending', 'Learning', 'Active', 'Archived'), '', ['class' => 'custom-select form-control-sm']) }}
    </p>

    <p>
        {{ Form::label('starting_key', 'Starting Key') }}
        {{ Form::email('starting_key', '', array('class' => 'form-control')) }}
    </p>

    <p>
        {{ Form::label('pitch_blown', 'Pitch Blown') }}
        {{ Form::email('pitch_blown', '', array('class' => 'form-control')) }}
    </p>

    <hr>

    <h3>Attachments</h3>

    <h4>Add Attachment</h4>
    <p>
        {{ Form::label('attachment-title', 'Title') }}
        {{ Form::text('attachment-title', '', array('class' => 'form-control')) }}
    </p>
    <p>
        {{ Form::label('attachment-category', 'Category') }}
        {{ Form::select('attachment-category', array('Learning Track', 'Demo', 'Sheet Music'), '', ['class' => 'custom-select form-control-sm']) }}
    </p>
    <p>
        {{ Form::label('attachment-upload', 'Upload Attachment') }}
        {{ Form::text('attachment-upload', '', array('class' => 'form-control')) }}
    </p>




    {{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

@endsection