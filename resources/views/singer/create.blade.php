@extends('layouts.app')

@section('title', 'Add Singer')

@section('content')

<h2>Add Singer</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
        </ul>
    </div>
@endif

{{ Form::open( array( 'route' => 'singers.index' ) ) }}
    
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