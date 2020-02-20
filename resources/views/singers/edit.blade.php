@extends('layouts.app')

@section('title', 'Edit - ' . $singer->name)

@section('content')

    <h2>Edit Singer</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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