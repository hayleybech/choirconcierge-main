@extends('layouts.page-blank')

@section('title', $singer->name . ' - Singers')

@section('page-content')

	<?php
	// Store CSS badge classes for categories
	$category_class = array(
	'Prospects'             => 'text-primary',
	'Archived Prospects'    => 'text-info',
	'Members'               => 'text-success',
	'Archived Members'      => 'text-danger',
	);
	?>
	<div class="row">
		<div class="col-md-7">

			<div class="profile-summary card">
				<div class="card-body">

					<img src="{{ $singer->user->getAvatarUrl('profile') }}" alt="{{ $singer->name }}" class="profile-image user-avatar-rounded">

					<div class="profile-summary-content">

						<div class="profile-header d-flex justify-content-between align-items-center">
							<h1>{{ $singer->name }}</h1>
							@can('update', $singer)
							<a href="{{route( 'singers.edit', ['singer' => $singer] )}}" class="btn btn-add btn-sm btn-secondary"><i class="fa fa-fw fa-edit"></i> Edit</a>
							@endcan
						</div>

						<div class="profile-item d-flex align-items-center">
							<div class="singer-part mr-4">
								@if( $singer->voice_part)
									<span class="badge badge-secondary badge-pill" {!! ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? 'style="background-color: '.$singer->voice_part->colour.';"' : '' !!}>{{ $singer->voice_part->title }}</span><br>
								@else
									<span class="badge badge-secondary badge-pill">No part</span>
								@endif
							</div>
							<span class="singer-category {{ $category_class[$singer->category->name] }}">
								<i class="fas fa-fw fa-circle mr-2"></i> {{ $singer->category->name }}</span>
							<br>
						</div>
						<div class="profile-item">
							<strong>Added</strong><br>
							<span class="text-muted">{{ $singer->created_at->toDayDateTimeString() }}</span><br>
						</div>
						<div class="profile-item">
							<strong>
								@if( $singer->onboarding_enabled )
								Onboarding enabled
								@else
								Onboarding disabled
								@endif
							</strong>
						</div>
						<div class="profile-item">
							<strong>Roles</strong><br>
							@foreach($singer->user->roles as $role)
							<span class="badge badge-secondary">{{$role->name}}</span>
							@endforeach
						</div>
					</div>

				</div>
			</div>

			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h2 class="h4">Member Profile</h2>
					@can('update', $singer->profile)
						<a href="{{route( 'profiles.edit', ['singer' => $singer, 'profile' => $singer->profile->id] )}}" class="btn btn-sm btn-outline-secondary ml-2"><i class="fa fa-fw fa-edit"></i> Edit</a>
					@elsecan('create', \App\Models\Profile::class)
						<a href="{{ route('profile.create', ['singer' => $singer, 'task' => 1]) }}" class="btn btn-sm btn-outline-secondary"><i class="fa fa-fw fa-plus"></i> Create</a>
					@endcan
				</div>
				<div class="card-body">

					<div class="row">

						<div class="col-md-5">
							<div class="profile-contact">
								<h3 class="h4">Contact</h3>
								<div class="profile-item">
									<i class="fas fa-fw fa-envelope mr-2"></i>{{ $singer->email }}
								</div>
								<div class="profile-item">
									<i class="fas fa-fw fa-phone mr-2"></i>{{ $singer->profile->phone ?? '?' }}
								</div>

								<h4 class="h5 mt-4">Emergency Contact</h4>
								<div class="profile-item">
									<i class="fas fa-fw fa-user mr-2"></i>{{ $singer->profile->ice_name ?? '?' }}
								</div>
								<div class="profile-item">
									<i class="fas fa-fw fa-phone mr-2"></i>{{ $singer->profile->ice_phone ?? '?' }}
								</div>
							</div>
						</div>
						<div class="col-md-7">
							<div class="profile-other">
								<h3 class="h4">Other Info</h3>
								@if( $singer->profile )
								<div class="profile-item">
										<i class="fas fa-fw fa-birthday-cake mr-2"></i>{{ $singer->profile->dob->toFormattedDateString() ?? '?' }}
									</div>

								<div class="profile-item">
									<strong>Reason for joining</strong><br>
									{{ $singer->profile->reason_for_joining ?? '' }}
								</div>
								<div class="profile-item">
									<strong>Referred by</strong><br>
									{{ $singer->profile->referrer ?? '' }}
								</div>
								<div class="profile-item">
									<strong>Profession</strong><br>
									{{ $singer->profile->profession ?? '' }}
								</div>
								<div class="profile-item">
									<strong>Other Skills</strong><br>
									{{ $singer->profile->skills ?? '' }}
								</div>
								@else
								<p>No Member Profile yet. @can('create', \App\Models\Profile::class)<a href="{{ route('profile.create', ['singer' => $singer, 'task' => 1]) }}">Create one now. </a>@endcan</p>
								@endif
							</div>
						</div>

					</div>


				</div>
			</div>



		</div>

		<div class="col-md-5">

			@if( Auth::user()->hasRole('Music Team') )
			<div class="card">
					<h3 class="card-header h4 d-flex justify-content-between align-items-center">
						Voice Placement
						@if($singer->placement)
						<a href="{{route( 'placements.edit', ['singer' => $singer, 'placement' => $singer->placement] )}}" class="btn btn-sm btn-outline-secondary ml-2"><i class="fa fa-fw fa-edit"></i> Edit</a>
						@endif
					</h3>
					@if( $singer->placement )
					<div class="card-body">
							<div class="mb-2">
								<strong>Pitch Skill</strong>
								<div class="d-flex align-items-center">
									<div class="mr-3">1</div>
									<div class="progress" style="width: 100%; height: 20px;">
										<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_pitch * 10 }}%" aria-valuenow="{{ $singer->placement->skill_pitch }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_pitch }}</div>
									</div>
									<div class="ml-3">10</div>
								</div>
							</div>
							<div class="mb-2">
								<strong>Harmony Skill</strong>
								<div class="d-flex align-items-center">
									<div class="mr-3">1</div>
									<div class="progress" style="width: 100%; height: 20px;">
										<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_harmony * 10 }}%" aria-valuenow="{{ $singer->placement->skill_harmony }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_harmony }}</div>
									</div>
									<div class="ml-3">10</div>
								</div>
							</div>
							<div class="mb-2">
								<strong>Performance Skill</strong>
								<div class="d-flex align-items-center">
									<div class="mr-3">1</div>
									<div class="progress" style="width: 100%; height: 20px;">
										<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_performance * 10 }}%" aria-valuenow="{{ $singer->placement->skill_performance }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_performance }}</div>
									</div>
									<div class="ml-3">10</div>
								</div>
							</div>
							<div class="mb-2">
								<strong>Sight Reading Skill</strong>
								<div class="d-flex align-items-center">
									<div class="mr-3">1</div>
									<div class="progress" style="width: 100%; height: 20px;">
										<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_sightreading * 10}}%" aria-valuenow="{{ $singer->placement->skill_sightreading }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_sightreading }}</div>
									</div>
									<div class="ml-3">10</div>
								</div>
							</div>
							<div class="mb-2">
								<strong>Voice Tone</strong>
								<div class="d-flex align-items-center">
									<i class="fas fa-fw fa-flute mr-3 text fa-lg"></i>
									<input type="range" class="custom-range" min="1" max="3" value="{{ $singer->placement->voice_tone }}" disabled>
									<i class="fas fa-fw fa-trumpet ml-3 fa-lg"></i>
								</div>
							</div>
							<div class="mb-2">
								<strong>Experience</strong>
								<div>{{ $singer->placement->experience ?? 'N/A' }}</div>
							</div>
							<div class="mb-2">
								<strong>Instruments</strong>
								<div>{{ $singer->placement->instruments ?? 'N/A' }}</div>
							</div>

						</div>
					@else
					<div class="card-body">
							<p>No Voice Placement yet. <a href="{{ route('placement.create', ['singer' => $singer, 'task' => 2]) }}">Create one now. </a></p>
						</div>
					@endif
				</div>
			@endif

			@if( $singer->onboarding_enabled && Auth::user()->isEmployee() )
				<div class="card">
					<h3 class="card-header h5">Tasks</h3>
					<div class="card-body">
						@foreach( $singer->tasks as $task )
							@if( $task->pivot->completed )
								<span class="profile-item d-flex justify-content-between align-items-center link-confirm disabled" >
									<div class="d-flex w-100 justify-content-between">
										<span><i class="far fa-fw fa-check-square"></i> {{ $task->name }}</span>
									</div>
								</span>
							@else
								@if(Auth::user()->hasRole($task->role->name))
									<a href="{{ route($task->route, ['singer' => $singer, 'task' => $task]) }}" class="profile-item d-flex justify-content-between align-items-center link-confirm" >
										<div class="d-flex w-100 justify-content-between">
											<span><i class="far fa-fw fa-square"></i> {{ $task->name }}</span>
										</div>
									</a>
								@else
									<div class="profile-item d-flex justify-content-between align-items-center disabled" >
										<div class="d-flex w-100 justify-content-between">
											<span><i class="far fa-fw fa-square"></i> {{ $task->name }}</span>
										</div>
									</div>
								@endif
							@endif
						@endforeach
					</div>
				</div>
			@endif



		</div>

	</div>

	<div class="row">

		<div class="col-md-6">

		</div>
		<div class="col-md-6">


		</div>

	</div>


@endsection