@extends('layouts.app')

@section('title', $singer->name . ' - Singers')

@section('content')

	<h2 class="display-4">{{$singer->name}} <a href="{{route( 'singers.edit', ['singer' => $singer] )}}" class="btn btn-add btn-sm btn-outline-primary"><i class="fa fa-fw fa-edit"></i> Edit</a></h2>

	@include('partials.flash')
	
	<p class="mb-2 text-muted singer-email">
		Email: {{ $singer->email }}<br>
		Added: {{$singer->created_at}}
	</p>

	<div class="card">
		<div class="card-header">
			<h3 class="h6">Tasks</h3>
		</div>
		<div class="list-group list-group-flush">
			@foreach( $singer->tasks as $task )
				@if( $task->pivot->completed )
					<span class="list-group-item list-group-item-action d-flex justify-content-between align-items-center link-confirm disabled" >
                <div class="d-flex w-100 justify-content-between">
                    <span><i class="fa fa-fw fa-check-square-o"></i> {{ $task->name }}</span>
				</div>
			</span>
				@else
					<a href="{{ route($task->route, ['singer' => $singer, 'task' => $task]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center link-confirm" >
						<div class="d-flex w-100 justify-content-between">
							<span><i class="fa fa-fw fa-square-o"></i> {{ $task->name }}</span>
						</div>
					</a>
				@endif
			@endforeach
		</div>
	</div>
	
	<div class="card">
		<div class="card-header">
			<h3 class="h6">Member Profile</h3>
		</div>
		@if( $singer->profile )
		<ul class="list-group list-group-flush">
			<li class="list-group-item">
				<strong>DOB:</strong> {{$singer->profile->dob}}
			</li>
			<li class="list-group-item">
				<strong>Phone:</strong> {{$singer->profile->phone}}
			</li>
			<li class="list-group-item">
				<strong>Emergency Contact Name:</strong> {{$singer->profile->ice_name}}
			</li>
			<li class="list-group-item">
				<strong>Emergency Contact Phone:</strong> {{$singer->profile->ice_phone}}
			</li>
			<li class="list-group-item">
				<strong>Reason for joining:</strong> {{$singer->profile->reason_for_joining}}
			</li>
			<li class="list-group-item">
				<strong>Referred by:</strong> {{$singer->profile->referrer}}
			</li>
			<li class="list-group-item">
				<strong>Profession:</strong> {{$singer->profile->profession}}
			</li>
			<li class="list-group-item">
				<strong>Other Skills:</strong> {{$singer->profile->skills}}
			</li>
		</ul>
		@else
		<div class="card-body">
			<p>No Member Profile yet. <a href="{{ route('profile.create', ['singer' => $singer, 'task' => 1]) }}">Create one now. </a></p>
		</div>
		@endif
	</div>
	
	<div class="card">
		<div class="card-header">
			<h3 class="h6">Voice Placement</h3>
		</div>
		@if( $singer->placement )
		<ul class="list-group list-group-flush">
			<li class="list-group-item">
				<strong>Experience:</strong> {{$singer->placement->experience}}
			</li>
			<li class="list-group-item">
				<strong>Instruments:</strong> {{$singer->placement->instruments}}
			</li>
			<li class="list-group-item">
				<strong>Pitch Skill:</strong> {{$singer->placement->skill_pitch}}
			</li>
			<li class="list-group-item">
				<strong>Harmony Skill:</strong> {{$singer->placement->skill_harmony}}
			</li>
			<li class="list-group-item">
				<strong>Performance Skill:</strong> {{$singer->placement->skill_performance}}
			</li>
			<li class="list-group-item">
				<strong>Sight Reading Skill:</strong> {{$singer->placement->skill_sightreading}}
			</li>
			<li class="list-group-item">
				<strong>Voice Tone (1 = Fluty, 3 = Brassy):</strong> {{$singer->placement->voice_tone}}
			</li>
			<li class="list-group-item">
				<strong>Voice Part:</strong> {{$singer->placement->voice_part}}
			</li>
		</ul>
		@else
		<div class="card-body">
			<p>No Voice Placement yet. <a href="{{ route('placement.create', ['singer' => $singer, 'task' => 2]) }}">Create one now. </a></p>
		</div>
		@endif
	</div>
	

@endsection