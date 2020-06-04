@extends('layouts.page')

@section('title', 'Add Riser Stack')
@section('page-title', 'Add Riser Stack')

@section('page-content')

    {{ Form::open( array( 'route' => 'stacks.index' ) ) }}

    <div class="card bg-light">
        <h3 class="card-header h4">Riser Stack Details</h3>

        <div class="card-body">

            <div class="form-group">
                {{ Form::label('title', 'Stack Title') }}
                {{ Form::text('title', '', array('class' => 'form-control')) }}
            </div>

            <riser-stack></riser-stack>

            <div class="row">

                @foreach($voice_parts as $part)
                <div class="col-md-3">
                    <holding-area title="{{$part->title}}" :part="{{$part->id}}" theme="primary" :singers="{{$part->singers->toJson()}}"></holding-area>
                </div>
                @endforeach
            </div>

        </div>

        <div class="card-footer">
            {{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
        </div>

    </div>

    {{ Form::close() }}

@endsection