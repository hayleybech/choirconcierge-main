@extends('layouts.app')

@section('title', 'Main menu')

@section('content')

<h2>Add Voice Placement</h2>

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

{{ Form::open( array( 'route' => array('placement', $singer->id) ) ) }}

	{{ Form::hidden('singer_id', $singer->id) }}
    
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
	{{ Form::select('voice_part', array('Tenor' => 'Tenor', 'Lead' => 'Lead', 'Baritone' => 'Baritone', 'Bass' => 'Bass'), array('class' => 'form-control')) }}
	</p>
	
	{{ Form::submit('Create', array( 'class' => 'btn btn-primary' )) }}
	
{{ Form::close() }}

@endsection