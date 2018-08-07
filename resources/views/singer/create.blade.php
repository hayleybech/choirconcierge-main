@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

<h2>Add Singer</h2>

{{ Form::open() }}
    
	<p>
	{{ Form::label('name', 'Name') }}
	{{ Form::text('name', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('email', 'E-Mail Address') }}
	{{ Form::email('email', '', array('class' => 'form-control')) }}
	</p>
	
	{{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
	
{{ Form::close() }}

@endsection