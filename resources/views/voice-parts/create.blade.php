@extends('layouts.page')

@section('title', 'Add Voice Part')
@section('page-title', 'Add Voice Part')

@section('page-content')

{{ Form::open( [ 'route' => 'voice-parts.store', 'method' => 'post' ] ) }}

	<div class="card bg-light">
		<div class="card-header">Voice Part Details</div>

		<div class="card-body">
			<p>
				{{ Form::label('title', 'Name') }}
				{{ Form::text('title', '', ['class' => 'form-control']) }}
			</p>

			<p>
				{{ Form::label('colour', 'Colour') }}
				{{ Form::color('colour', '', ['class' => 'form-control']) }}
			</p>

		</div>

		<div class="card-footer">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-fw fa-check"></i> Create
			</button>
			<a href="{{ route('voice-parts.index') }}" class="btn btn-outline-secondary">
				<i class="fa fa-fw fa-times"></i> Cancel
			</a>
		</div>
	</div>

{{ Form::close() }}

@endsection