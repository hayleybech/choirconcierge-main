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

            <riser-stack :initial-rows="{{$stack->rows}}" :initial-cols="{{ $stack->columns }}" :initial-front-row-length="{{ $stack->front_row_length }}" :initial-singers="{{ $stack->singers->toJson() }}"></riser-stack>

            <div class="row">

                @foreach($voice_parts as $part)
                    <div class="col-md-3">
                        <holding-area title="{{$part->title}}" :part="{{$part->id}}" theme="primary" :singers="{{$part->singers->toJson()}}"></holding-area>
                    </div>
                @endforeach
            </div>

        </div>

        <div class="card-footer">
            {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}
        </div>

    </div>

    {{ Form::close() }}

@endsection