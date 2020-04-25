@extends('layouts.page')

@section('title', 'Add Voice Placement - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( array( 'route' => array('placement', $singer->id) ) ) }}

	{{ Form::hidden('singer_id', $singer->id) }}

	<div class="card bg-light">
		<h3 class="card-header h4">Add Voice Placement</h3>

		<div class="card-body">
			<p>
				{{ Form::label('experience', 'Experience') }}
				{{ Form::text('experience', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('instruments', 'Instruments') }}
				{{ Form::text('instruments', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('skill_pitch', 'Pitch Skill (1-5)') }}
				{{ Form::number('skill_pitch', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('skill_harmony', 'Harmony Skill (1-5)') }}
				{{ Form::number('skill_harmony', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('skill_performance', 'Performance Skill (1-5)') }}
				{{ Form::number('skill_performance', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('skill_sightreading', 'Sight Reading Skill (1-5)') }}
				{{ Form::number('skill_sightreading', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('voice_tone', 'Voice Tone (1 = Fluty, 3 = Brassy)') }}
				{{ Form::number('voice_tone', '', array('class' => 'form-control')) }}
			</p>

			<p>
				{{ Form::label('voice_part', 'Voice Part') }}
				{{ Form::select('voice_part', $voice_parts, '', array('class' => 'custom-select')) }}
			</p>

		</div>

		<div class="card-footer">
			{{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
		</div>
	</div>

{{ Form::close() }}

@endsection