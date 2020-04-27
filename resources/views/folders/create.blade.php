@extends('layouts.page')

@section('title', 'Add Folder')
@section('page-title', 'Add Folder')

@section('page-content')

    {{ Form::open( array( 'route' => 'folders.index' ) ) }}

    <div class="card bg-light">
        <h3 class="card-header h4">Folder Details</h3>

        <div class="card-body">

            <div class="form-group">
                {{ Form::label('title', 'Folder Title') }}
                {{ Form::text('title', '', array('class' => 'form-control')) }}
            </div>

        </div>

        <div class="card-footer">
            {{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
        </div>
    </div>

    {{ Form::close() }}

@endsection