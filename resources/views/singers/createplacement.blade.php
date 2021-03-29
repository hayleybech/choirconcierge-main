@extends('layouts.page')

@section('title', 'Add Voice Placement - ' . $singer->name)
@section('page-title', $singer->name)

@section('page-content')

	{{ Form::open( [ 'route' => ['singers.placements.store', $singer->id] ] ) }}

	{{ Form::hidden('singer_id', $singer->id) }}

	<div class="row">
		<div class="col-md-6">

			<div class="card">
				<h3 class="card-header h4">Add Voice Placement</h3>

				<div class="card-body">
					<div class="form-group">
						<x-inputs.text label="Experience" id="experience" name="experience"></x-inputs.text>
					</div>

					<div class="form-group">
						<x-inputs.text label="Instruments" id="instruments" name="instruments"></x-inputs.text>
					</div>

					<div class="form-group">
						<x-inputs.range label="Pitch Skill" id="skill_pitch" name="skill_pitch" min="1" max="5"></x-inputs.range>
					</div>

					<div class="form-group">
						<x-inputs.range label="Harmony Skill" id="skill_harmony" name="skill_harmony" min="1" max="5"></x-inputs.range>
					</div>

					<div class="form-group">
						<x-inputs.range label="Performance Skill" id="skill_performance" name="skill_performance" min="1" max="5"></x-inputs.range>
					</div>

					<div class="form-group">
						<x-inputs.range label="Sight Reading Skill" id="skill_sightreading" name="skill_sightreading" min="1" max="5"></x-inputs.range>
					</div>

					<div class="form-group">
						<x-inputs.range label="Voice Tone" id="voice_tone" name="voice_tone" min="1" max="3">
							<x-slot name="minDesc">
								<i class="fas fa-fw fa-flute fa-lg"></i>
								<small class="form-text text-muted">Fluty</small>
							</x-slot>
							<x-slot name="maxDesc">
								<i class="fas fa-fw fa-trumpet fa-lg"></i>
								<small class="form-text text-muted">Brassy</small>
							</x-slot>
						</x-inputs.range>
					</div>

					<div class="form-group">
						<x-inputs.select label="Voice Part" id="voice_part_id" name="voice_part_id" :options="$voice_parts"></x-inputs.select>
					</div>

				</div>

				<div class="card-footer">
					<button type="submit" class="btn btn-primary">
						<i class="fa fa-fw fa-check"></i> Create
					</button>
					<a href="{{ route('singers.show', [$singer]) }}" class="btn btn-link text-danger">
						<i class="fa fa-fw fa-times"></i> Cancel
					</a>
				</div>
			</div>

		</div>
	</div>

{{ Form::close() }}

@endsection