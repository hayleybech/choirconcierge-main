@extends('layouts.page')

@section('title', $singer->name . ' - Singers')
@section('page-title', $singer->name)
@section('page-action')
	<a href="{{route( 'singers.edit', ['singer' => $singer] )}}" class="btn btn-add btn-sm btn-light"><i class="fa fa-fw fa-edit"></i> Edit</a>
@endsection
@section('page-lead')
	<span class="badge badge-light">{{ $singer->category->name }}</span><br>
	@if( $singer->placement)<i class="fa fa-fw fa-users"></i> {{ $singer->placement->voice_part }} <br>@endif
	{{ $singer->email }}<br>
	Added: {{$singer->created_at}}<br>
	<strong>
		@if( $singer->onboarding_enabled )
			Onboarding enabled
		@else
			Onboarding disabled
		@endif
	</strong>
@endsection

@section('page-content')

	<div class="card bg-light">
		<h3 class="card-header h5">Tasks</h3>
		<div class="list-group list-group-flush">
			@foreach( $singer->tasks as $task )
				@if( $task->pivot->completed )
					<span class="list-group-item list-group-item-action d-flex justify-content-between align-items-center link-confirm disabled" >
                <div class="d-flex w-100 justify-content-between">
                    <span><i class="far fa-fw fa-check-square"></i> {{ $task->name }}</span>
				</div>
			</span>
				@else
					<a href="{{ route($task->route, ['singer' => $singer, 'task' => $task]) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center link-confirm" >
						<div class="d-flex w-100 justify-content-between">
							<span><i class="far fa-fw fa-square"></i> {{ $task->name }}</span>
						</div>
					</a>
				@endif
			@endforeach
		</div>
	</div>
	
	<div class="card bg-light">
		<h3 class="card-header h5">Member Profile</h3>
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
	
	<div class="card bg-light">
		<h3 class="card-header h5">Voice Placement</h3>
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