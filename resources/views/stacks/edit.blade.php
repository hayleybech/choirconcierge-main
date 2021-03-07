@extends('layouts.page')

@section('title', 'Edit - ' . $stack->title)
@section('page-title', $stack->title)

@section('page-content')

    {{ Form::open( [ 'route' => ['stacks.show', $stack->id], 'method' => 'put' ] ) }}

    <div class="card">
        <h3 class="card-header h4">Edit Riser Stack</h3>

        <div class="card-body">

            <div class="form-group">
                {{ Form::label('title', 'Stack Title') }}
                {{ Form::text('title', $stack->title, ['class' => 'form-control']) }}
            </div>

            <riser-stack
                :initial-rows="{{$stack->rows}}"
                :initial-cols="{{ $stack->columns }}"
                :initial-front-row-length="{{ $stack->front_row_length }}"
                :initial-singers="{{ $stack->singers->toJson() }}"
                :initial-voice-parts="{{ $voice_parts->toJson() }}"
                :initial-front-row-on-floor="{{ var_export($stack->front_row_on_floor, true) }}"
            ></riser-stack>

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-fw fa-check"></i> Save
            </button>
            <a href="{{ route('stacks.show', [$stack]) }}" class="btn btn-link text-danger">
                <i class="fa fa-fw fa-times"></i> Cancel
            </a>
        </div>

    </div>

    {{ Form::close() }}

@endsection