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
                {{ Form::text('title', old('title', $stack->title), ['class' => 'form-control']) }}
            </div>

            <riser-stack
                :initial-voice-parts="{{ $voice_parts->toJson() }}"
                :initial-singers="{{ old('singer_positions', $stack->singers->toJson()) }}"
                :initial-rows="{{ old('rows', $stack->rows )}}"
                :initial-cols="{{ old('columns', $stack->columns) }}"
                :initial-front-row-length="{{ old('front_row_length', $stack->front_row_length) }}"
                :initial-front-row-on-floor="{{ json_encode(checkbox_old('front_row_on_floor', true, $stack->front_row_on_floor)) }}"
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