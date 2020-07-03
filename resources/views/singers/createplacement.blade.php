@extends('layouts.page')

@section('title', 'Add Voice Placement - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( array( 'route' => array('placement', $singer->id) ) ) }}

	{{ Form::hidden('singer_id', $singer->id) }}

	<div class="row">
		<div class="col-md-6">

			<div class="card">
				<h3 class="card-header h4">Add Voice Placement</h3>

				<div class="card-body">
					<div class="form-group">
						{{ Form::label('experience', 'Experience') }}
						{{ Form::text('experience', '', ['class' => 'form-control']) }}
					</div>

					<div class="form-group">
						{{ Form::label('instruments', 'Instruments') }}
						{{ Form::text('instruments', '', ['class' => 'form-control']) }}
					</div>

					<div class="form-group">
						{{ Form::label('skill_pitch', 'Pitch Skill') }}
						<div class="d-flex align-items-start">
							<div class="mr-4">
								1
							</div>
							<input id="skill_pitch" name="skill_pitch" type="range" class="custom-range" min="1" max="5" value="">
							<div class="ml-4">
								5
							</div>
						</div>
					</div>

					<div class="form-group">
						{{ Form::label('skill_harmony', 'Harmony Skill') }}
						<div class="d-flex align-items-start">
							<div class="mr-4">
								1
							</div>
							<input id="skill_harmony" name="skill_harmony" type="range" class="custom-range" min="1" max="5" value="">
							<div class="ml-4">
								5
							</div>
						</div>
					</div>

					<div class="form-group">
						{{ Form::label('skill_performance', 'Performance Skill') }}
						<div class="d-flex align-items-start">
							<div class="mr-4">
								1
							</div>
							<input id="skill_performance" name="skill_performance" type="range" class="custom-range" min="1" max="5" value="">
							<div class="ml-4">
								5
							</div>
						</div>
					</div>

					<div class="form-group">
						{{ Form::label('skill_sightreading', 'Sight Reading Skill') }}
						<div class="d-flex align-items-start">
							<div class="mr-4">
								1
							</div>
							<input id="skill_sightreading" name="skill_sightreading" type="range" class="custom-range" min="1" max="5" value="">
							<div class="ml-4">
								5
							</div>
						</div>
					</div>

					<div class="form-group">
						{{ Form::label('voice_tone', 'Voice Tone') }}
						<div class="d-flex align-items-start">
							<div>
								<i class="fas fa-fw fa-flute fa-lg mr-3"></i>
								<small class="form-text text-muted">Fluty</small>
							</div>
							<input id="voice_tone" name="voice_tone" type="range" class="custom-range" min="1" max="3" value="">
							<div>
								<i class="fas fa-fw fa-trumpet fa-lg ml-3"></i>
								<small class="form-text text-muted">Brassy</small>
							</div>
						</div>
					</div>

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

		</div>
	</div>

{{ Form::close() }}

@endsection