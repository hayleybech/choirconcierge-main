@extends('layouts.page')

@section('title', 'Edit - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

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