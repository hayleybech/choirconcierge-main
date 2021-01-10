@extends('layouts.page')

@section('title', 'Add Folder')
@section('page-title', 'Add Folder')

@section('page-content')

    {{ Form::open( [ 'route' => 'folders.index' ] ) }}

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h3 class="card-header h4">Folder Details</h3>

                <div class="card-body">

                    <div class="form-group">
                        {{ Form::label('title', 'Folder Title') }}
                        {{ Form::text('title', '', ['class' => 'form-control']) }}
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Create
                    </button>
                    <a href="{{ route('folders.index') }}" class="btn btn-link text-danger">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{ Form::close() }}

@endsection