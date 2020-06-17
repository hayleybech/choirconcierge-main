@extends('layouts.page')

@section('title', 'Add Voice Placement - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( [ 'route' => ['placements.update', [$singer, $placement]], 'method' => 'put' ] ) }}

	<div class="card bg-light">
		<h3 class="card-header h4">Edit Voice Placement</h3>

		<div class="card-body">
			<p>
				{{ Form::label('experience', 'Experience') }}
				{{ Form::text('experience', $placement->experience, ['class' => 'form-control']) }}
			</p>

			<p>
				{{ Form::label('instruments', 'Instruments') }}
				{{ Form::text('instruments', $placement->instruments, ['class' => 'form-control']) }}
			</p>

			<p>
				{{ Form::label('skill_pitch', 'Pitch Skill (1-5)') }}
				{{ Form::number('skill_pitch', $placement->skill_pitch, ['class' => 'form-control']) }}
			</p>

			<p>
				{{ Form::label('skill_harmony', 'Harmony Skill (1-5)') }}
				{{ Form::number('skill_harmony', $placement->skill_harmony, ['class' => 'form-control']) }}
			</p>

			<p>
				{{ Form::label('skill_performance', 'Performance Skill (1-5)') }}
				{{ Form::number('skill_performance', $placement->skill_performance, ['class' => 'form-control']) }}
			</p>

			<p>
				{{ Form::label('skill_sightreading', 'Sight Reading Skill (1-5)') }}
				{{ Form::number('skill_sightreading', $placement->skill_sightreading, ['class' => 'form-control']) }}
			</p>

			<p>
				{{ Form::label('voice_tone', 'Voice Tone (1 = Fluty, 3 = Brassy)') }}
				{{ Form::number('voice_tone', $placement->voice_tone, ['class' => 'form-control']) }}
			</p>

		</div>

		<div class="card-footer">
			<button type="submit" class="btn btn-primary">
				<i class="fa fa-fw fa-check"></i> Save
			</button>
			<a href="{{ route('singers.show', [$singer]) }}" class="btn btn-outline-secondary">
				<i class="fa fa-fw fa-times"></i> Cancel
			</a>
		</div>
	</div>

{{ Form::close() }}

@endsection