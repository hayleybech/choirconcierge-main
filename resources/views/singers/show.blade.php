@extends('layouts.page-blank')

@section('title', $singer->user->name . ' - Singers')

@section('page-content')

	<div class="row">
		<div class="col-md-7">

			<div class="card">
				<div class="card-body py-4">

					<div class="row">

						<div class="col-xl-5 col-lg-5">
							<img src="{{ $singer->user->getAvatarUrl('profile') }}" alt="{{ $singer->user->name }}" class="mb-4 mr-2 mw-100 img-rounded">
						</div>

						<div class="col-xl-7 col-lg-7">

							<div class="d-flex justify-content-between align-items-center mb-2">
								<h1 class="h3 mb-0">{{ $singer->user->name }}</h1>
								@can('update', $singer)
									<a href="{{route( 'singers.edit', ['singer' => $singer] )}}" class="btn btn-add btn-sm btn-primary"><i class="fa fa-fw fa-edit"></i> Edit Membership</a>
								@endcan
							</div>

							<div class="mb-1 d-flex align-items-center">

								<div class="singer-part mr-4">
									@if( $singer->voice_part)
										<span class="badge badge-light badge-pill" {!! ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? 'style="background-color: '.$singer->voice_part->colour.';"' : '' !!}>{{ $singer->voice_part->title }}</span><br>
									@else
										<span class="badge badge-light badge-pill">No part</span>
									@endif
								</div>

								<span class="singer-category text-{{ $singer->category->colour }}">
									<i class="fas fa-fw fa-circle mr-2"></i>
									{{ $singer->category->name }}
								</span>

								<br>
							</div>

							<div>
								@can('create', \App\Models\Singer::class)
									<div class="dropdown mt-2 mb-3">
										<button class="btn btn-secondary btn-sm force-xs dropdown-toggle" type="button" id="moveDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											Move to
										</button>
										<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
											@foreach($categories as $category)
												<a class="dropdown-item text-{{ $category->colour }}" href="{{ route( 'singers.categories.update', ['singer' => $singer, 'move_category' => $category->id] ) }}">
													<i class="fas fa-fw fa-circle mr-2"></i>
													{{ $category->name }}
												</a>
											@endforeach
										</div>
									</div>
								@endcan
							</div>

							<div class="mb-1">
								<strong>Roles</strong><br>
								@foreach($singer->user->roles as $role)
									<span class="badge badge-dark">{{$role->name}}</span>
								@endforeach
							</div>

							<div class="mb-1">
								<strong>
									@if( $singer->onboarding_enabled )
										Onboarding enabled
									@else
										Onboarding disabled
									@endif
								</strong>
							</div>

							<div class="mb-1">
								<strong>Reason for joining</strong><br>
								{{ $singer->reason_for_joining ?? '' }}
							</div>
							<div class="mb-1">
								<strong>Referred by</strong><br>
								{{ $singer->referrer ?? '' }}
							</div>
							<div class="mb-1">
								<strong>Notes / Membership Details</strong><br>
								{{ $singer->membership_details ?? '' }}
							</div>

							<div class="mb-1 text-xs text-muted">
								<small>
									<strong>Member Since: </strong> <span>{{ $singer->joined_at->diffForHumans() }} ({{ $singer->joined_at->format(config('app.formats.date_lg')) }})</span> <br />
									<strong>Added: </strong> <span>{{ $singer->created_at->diffForHumans() }}</span> |
									<strong>Last Login: </strong>
									<span>
									@if( ! $singer->user->last_login )
											Never
										@else
											{{ $singer->user->last_login->diffForHumans() }}
										@endif
								</span>
								</small>
							</div>

						</div>
					</div>


				</div>
			</div>

			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
					<h2 class="h4">User Profile</h2>
					@if(auth()->user()->is($singer->user))
						<a href="{{ route( 'accounts.edit') }}" class="btn btn-sm btn-secondary ml-2"><i class="fa fa-fw fa-edit"></i> Edit Profile</a>
					@endif
				</div>
				<div class="card-body">

					<div class="row">

						<div class="col-md-5">
							<div class="profile-contact">
								<h3 class="h4">Contact</h3>
								<p class="mb-1">
									<i class="fas fa-fw fa-envelope mr-2"></i>{{ $singer->user->email }}
								</p>
								<div class="mb-1">
									<i class="fas fa-fw fa-phone mr-2"></i>{{ $singer->user->phone ?? '?' }}
								</div>

								<h4 class="h5 mt-4">Emergency Contact</h4>
								<div class="mb-1">
									<i class="fas fa-fw fa-user mr-2"></i>{{ $singer->user->ice_name ?? '?' }}
								</div>
								<div class="mb-1">
									<i class="fas fa-fw fa-phone mr-2"></i>{{ $singer->user->ice_phone ?? '?' }}
								</div>

								<h3 class="h4 mt-4">Address</h3>
								<p>
									{{ $singer->user->address_street_1 ?? 'No address' }}<br>
									@if( isset($singer->user->address_street_2) && $singer->user->address_street_2 !== ''){{ $singer->user->address_street_2 ?? 'No address' }}<br>@endif
									{{ $singer->user->address_suburb ? $singer->user->address_suburb . ', ' : '' }}{{ $singer->user->address_state ?? '' }} {{ $singer->user->address_postcode ?? '' }}
								</p>
							</div>
						</div>
						<div class="col-md-7">
							<div class="profile-other">
								<h3 class="h4">Other Info</h3>
								<div class="mb-1">
									<strong>Date of Birth: </strong>
									{{ $singer->user->dob?->format(config('app.formats.date_md')) ?? '?' }}
								</div>

								<div class="mb-1">
									<strong>Height: </strong>
									{{ round( $singer->user->height, 2) }} cm
								</div>

								<div class="mb-1">
									<strong>Profession</strong><br>
									{{ $singer->user->profession ?? '' }}
								</div>
								<div class="mb-1">
									<strong>Other Skills</strong><br>
									{{ $singer->user->skills ?? '' }}
								</div>
								<div class="mb-1">
									<strong>BHA Member ID</strong><br>
									{{ $singer->user->bha_id > 0 ? $singer->user->bha_id : '' }}
								</div>
							</div>
						</div>

					</div>


				</div>
			</div>



		</div>

		<div class="col-md-5">

			@can('view', $singer->placement)
			<div class="card">
				<h3 class="card-header h4 d-flex justify-content-between align-items-center">
					Voice Placement
					@can('update', $singer->placement)
					<a href="{{route( 'singers.placements.edit', ['singer' => $singer, 'placement' => $singer->placement] )}}" class="btn btn-sm btn-secondary ml-2"><i class="fa fa-fw fa-edit"></i> Edit</a>
					@endcan
				</h3>
				<div class="card-body">
						<div class="mb-2">
							<strong>Pitch Skill</strong>
							<div class="d-flex align-items-center">
								<div class="mr-3">1</div>
								<div class="progress" style="width: 100%; height: 20px;">
									<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_pitch / 5 * 100 }}%" aria-valuenow="{{ $singer->placement->skill_pitch }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_pitch }}</div>
								</div>
								<div class="ml-3">5</div>
							</div>
						</div>
						<div class="mb-2">
							<strong>Harmony Skill</strong>
							<div class="d-flex align-items-center">
								<div class="mr-3">1</div>
								<div class="progress" style="width: 100%; height: 20px;">
									<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_harmony / 5 * 100 }}%" aria-valuenow="{{ $singer->placement->skill_harmony }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_harmony }}</div>
								</div>
								<div class="ml-3">5</div>
							</div>
						</div>
						<div class="mb-2">
							<strong>Performance Skill</strong>
							<div class="d-flex align-items-center">
								<div class="mr-3">1</div>
								<div class="progress" style="width: 100%; height: 20px;">
									<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_performance / 5 * 100 }}%" aria-valuenow="{{ $singer->placement->skill_performance }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_performance }}</div>
								</div>
								<div class="ml-3">5</div>
							</div>
						</div>
						<div class="mb-2">
							<strong>Sight Reading Skill</strong>
							<div class="d-flex align-items-center">
								<div class="mr-3">1</div>
								<div class="progress" style="width: 100%; height: 20px;">
									<div class="progress-bar" role="progressbar" style="width: {{ $singer->placement->skill_sightreading / 5 * 100}}%" aria-valuenow="{{ $singer->placement->skill_sightreading }}" aria-valuemin="1" aria-valuemax="5">{{ $singer->placement->skill_sightreading }}</div>
								</div>
								<div class="ml-3">5</div>
							</div>
						</div>
						<div class="mb-2">
							<x-inputs.range label="Voice Tone" id="voice_tone" name="voice_tone" min="1" max="3" value="{{ $singer->placement->voice_tone }}" disabled label-class="font-weight-bold">
								<x-slot name="minDesc"><i class="fas fa-fw fa-flute fa-lg"></i></x-slot>
								<x-slot name="maxDesc"><i class="fas fa-fw fa-trumpet fa-lg"></i></x-slot>
							</x-inputs.range>
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
				</div>
			@elsecan('create', \App\Models\Placement::class)
			<div class="card">
				<h3 class="card-header h4 d-flex justify-content-between align-items-center">Voice Placement</h3>
				<div class="card-body">
					<p>No Voice Placement yet. <a href="{{ route('singers.placements.create', ['singer' => $singer, 'task' => 2]) }}">Create one now. </a></p>
				</div>
			</div>
			@endcan

			@if( $singer->onboarding_enabled && Auth::user()->can('viewAny', \App\Models\Task::class) )
				<div class="card">
					<h3 class="card-header h5">Tasks</h3>
					<div class="card-body">
						@foreach( $singer->tasks as $task )
							@if( $task->pivot->completed )
								<span class="mb-1 d-flex justify-content-between align-items-center disabled">
									<div class="d-flex w-100 justify-content-between">
										<span><i class="far fa-fw fa-check-square"></i> {{ $task->name }}</span>
									</div>
								</span>
							@else
								@if(Auth::user()->hasRole($task->role->name))
									<link-confirm href="{{ route($task->route, ['singer' => $singer, 'task' => $task]) }}" description="{{ $task->name }}" class="mb-1 d-flex justify-content-between align-items-center">
										<div class="d-flex w-100 justify-content-between">
											<span><i class="far fa-fw fa-square"></i> {{ $task->name }}</span>
										</div>
									</link-confirm>
								@else
									<div class="mb-1 d-flex justify-content-between align-items-center disabled" >
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