@extends('layouts.page')

@section('title', 'Add Singer')
@section('page-title', 'Add Singer')

@section('page-content')

{{ Form::open( array( 'route' => 'singers.index' ) ) }}

	<div class="card bg-light">
		<div class="card-header">Singer Details</div>

		<div class="card-body">
			<p>
				{{ Form::label('name', 'Name') }}
				{{ Form::text('name', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('email', 'E-Mail Address') }}
				{{ Form::email('email', '', array('class' => 'form-control')) }}
			</p>
		</div>

		<div class="card-footer">
			{{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
		</div>
	</div>

{{ Form::close() }}

@endsection