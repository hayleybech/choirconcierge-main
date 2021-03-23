@extends('layouts.page')

@section('title', 'Edit - ' . $folder->title)
@section('page-title', $folder->title)

@section('page-content')

    {{ Form::open( [ 'route' => ['folders.show', $folder], 'method' => 'put' ] ) }}

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <h3 class="card-header h4">Folder Details</h3>

                <div class="card-body">

                    <div class="form-group">
                        <x-inputs.text label="Folder Title" id="title" name="title" value="{{ $folder->title }}"></x-inputs.text>
                    </div>

                </div>

                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-fw fa-check"></i> Save
                    </button>
                    <a href="{{ route('folders.show', [$folder]) }}" class="btn btn-outline-secondary">
                        <i class="fa fa-fw fa-times"></i> Cancel
                    </a>
                </div>
            </div>

        </div>
    </div>

    {{ Form::close() }}

@endsection