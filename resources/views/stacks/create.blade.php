@extends('layouts.page')

@section('title', 'Add Riser Stack')
@section('page-title', 'Add Riser Stack')

@section('page-content')

    {{ Form::open( array( 'route' => 'riser-stacks.index' ) ) }}

    <div class="card bg-light">
        <h3 class="card-header h4">Riser Stack Details</h3>

        <div class="card-body">

            <div class="form-group">
                {{ Form::label('title', 'Stack Title') }}
                {{ Form::text('title', '', array('class' => 'form-control')) }}
            </div>

            <riser-stack :initial-voice-parts="{{ $voice_parts->toJson() }}"></riser-stack>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-fw fa-check"></i> Create
            </button>
            <a href="{{ route('riser-stacks.index') }}" class="btn btn-outline-secondary">
                <i class="fa fa-fw fa-times"></i> Cancel
            </a>
        </div>

    </div>

    {{ Form::close() }}

@endsection