@extends('layouts.app')

@section('title', 'Edit - ' . $singer->name)

@section('content')

    <div class="jumbotron bg-light">
        <h2 class="display-4">{{$singer->name}}</h2>
    </div>

    @include('partials.flash')

    {{ Form::open( array( 'route' => ['singers.show', $singer->id], 'method' => 'put' ) ) }}

    <div class="card bg-light">
        <div class="card-header">Edit Singer</div>

        <div class="card-body">
            <p>
                {{ Form::label('name', 'Name') }}
                {{ Form::text('name', $singer->name, array('class' => 'form-control')) }}
            </p>

            <p>
                {{ Form::label('email', 'E-Mail Address') }}
                {{ Form::email('email', $singer->email, array('class' => 'form-control')) }}
            </p>
        </div>

        <div class="card-footer">
            {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}
        </div>

    </div>

    {{ Form::close() }}

@endsection