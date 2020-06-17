@extends('layouts.page')

@section('title', 'Add Member Profile - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( array( 'route' => array('profile', $singer->id) ) ) }}

	{{ Form::hidden('singer_id', $singer->id) }}

	<div class="card bg-light">
		<h3 class="card-header h4">Add Member Profile</h3>

		<div class="card-body">
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

		</div>

		<div class="card-footer">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-fw fa-check"></i> Create
			</button>
			<a href="{{ route('singers.show', [$singer]) }}" class="btn btn-outline-secondary">
				<i class="fa fa-fw fa-times"></i> Cancel
			</a>
		</div>
	</div>

{{ Form::close() }}

@endsection