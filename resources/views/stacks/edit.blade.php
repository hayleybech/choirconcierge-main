@extends('layouts.page')

@section('title', 'Edit - ' . $stack->title)
@section('page-title', $stack->title)

@section('page-content')

    {{ Form::open( array( 'route' => ['stacks.show', $stack->id], 'method' => 'put' ) ) }}

    <div class="card bg-light">
        <h3 class="card-header h4">Edit Riser Stack</h3>

        <div class="card-body">

            <div class="form-group">
                {{ Form::label('title', 'Stack Title') }}
                {{ Form::text('title', $stack->title, array('class' => 'form-control')) }}
            </div>

            <riser-stack
                :initial-rows="{{$stack->rows}}"
                :initial-cols="{{ $stack->columns }}"
                :initial-front-row-length="{{ $stack->front_row_length }}"
                :initial-singers="{{ $stack->singers->toJson() }}"
                :initial-voice-parts="{{ $voice_parts->toJson() }}"
            ></riser-stack>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-fw fa-check"></i> Save
            </button>
            <a href="{{ route('stacks.show', [$stack]) }}" class="btn btn-outline-secondary">
                <i class="fa fa-fw fa-times"></i> Cancel
            </a>
        </div>

    </div>

    {{ Form::close() }}

@endsection