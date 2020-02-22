@extends('layouts.app')

@section('title', 'Edit - ' . $singer->name)

@section('content')

    <h2 class="display-4 mb-4">{{$singer->name}}</h2>

    <h3>Edit Singer</h3>
    @include('partials.flash')

    {{ Form::open( array( 'route' => ['singers.show', $singer->id], 'method' => 'put' ) ) }}

    <p>
        {{ Form::label('name', 'Name') }}
        {{ Form::text('name', $singer->name, array('class' => 'form-control')) }}
    </p>

    <p>
        {{ Form::label('email', 'E-Mail Address') }}
        {{ Form::email('email', $singer->email, array('class' => 'form-control')) }}
    </p>

    {{ Form::submit('Save', array( 'class' => 'btn btn-primary' )) }}

    {{ Form::close() }}

@endsection