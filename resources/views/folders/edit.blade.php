@extends('layouts.page')

@section('title', 'Edit - ' . $folder->title)
@section('page-title', $folder->title)

@section('page-content')

    {{ Form::open( array( 'route' => ['folders.show', $folder], 'method' => 'put' ) ) }}

    <div class="card bg-light">
        <h3 class="card-header h4">Folder Details</h3>

        <div class="card-body">

            <div class="form-group">
                {{ Form::label('title', 'Folder Title') }}
                {{ Form::text('title', $folder->title, array('class' => 'form-control')) }}
            </div>

        </div>

        <div class="card-footer">
            {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}
        </div>
    </div>

    {{ Form::close() }}

@endsection