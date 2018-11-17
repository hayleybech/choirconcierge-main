@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

<h2>Add Member Profile</h2>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
		@foreach ($errors->all() as $error)
			<li>{{ $error }}</li>
		@endforeach
        </ul>
    </div>
@endif

<h3>{{$singer->name}}</h3>

{{ Form::open( array( 'route' => array('profile', $singer->id) ) ) }}

	{{ Form::hidden('singer_id', $singer->id) }}
    
	<p>
	{{ Form::label('dob', 'Date of Birth') }}
	{{ Form::date('dob', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('phone', 'Phone') }}
	{{ Form::text('phone', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('ice_name', 'Emergency Contact Name') }}
	{{ Form::text('ice_name', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('ice_phone', 'Emergency Contact Phone') }}
	{{ Form::text('ice_phone', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('reason_for_joining', 'Why are you joining?') }}
	{{ Form::text('reason_for_joining', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('referrer', 'Where did you hear about us?') }}
	{{ Form::text('referrer', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('profession', 'What is your profession?') }}
	{{ Form::text('profession', '', array('class' => 'form-control')) }}
	</p>
	
	<p>
	{{ Form::label('skills', 'What non-musical skills do you have?') }}
	{{ Form::text('skills', '', array('class' => 'form-control')) }}
	</p>
	
	{{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
	
{{ Form::close() }}

@endsection