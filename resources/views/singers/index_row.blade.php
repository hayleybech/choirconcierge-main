	<?php
	// Store CSS badge classes for categories
	$category_class = array(
		'Prospects'             => 'badge-primary',
		'Archived Prospects'    => 'badge-danger',
		'Members'               => 'badge-success',
		'Archived Members'      => 'badge-warning',
	);
	?>
	<div class="r-table__row row--singer">
        <div class="r-table__cell col--mark">
            <input type="checkbox" />
        </div>
		<div class="r-table__cell col--title">
			<a href="{{route('singers.show', ['singer' => $singer])}}">
				<img src="{{ $singer->user->getFirstMediaUrl('avatar', 'thumb') }}" alt="{{ $singer->name }}" class="user-avatar">
			</a>
			<div class="item-title-wrapper">
				<a class="item-title" href="{{route('singers.show', ['singer' => $singer])}}">
					{{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
				</a>
				<div class="text-muted singer-email">{{ $singer->email }}</div>
				<div class="singer-phone text-muted">{{ ( isset($singer->profile->phone) && $singer->profile->phone !== '' ) ? $singer->profile->phone : 'No phone' }}</div>
			</div>
		</div>
        <div class="r-table__cell singer-col--part">
			<span class="singer-part">
				<span class="badge badge-secondary"><i class="fa fa-users"></i> {{ ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? $singer->voice_part->title : 'No part' }}</span><br>
			</span>
		</div>
		<div class="r-table__cell singer-col--category">
			<span class="singer-category badge badge-pill {{ $category_class[$singer->category->name] }}">{{ explode( ' ', $singer->category->name )[0] }}</span>
		</div>
		<div class="r-table__cell singer-col--progress">
			<!--<div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>-->
			@if( $singer->onboarding_enabled && Auth::user()->isEmployee() )
				@foreach( $singer->tasks as $task )
					@if( $task->pivot->completed )
						@continue
					@else
						@php
							if( $task->type === 'form' ){
								$btn_style = 'btn-primary';
								$icon_complete = 'fa-file';
								$action = 'Start';
							} else {
								$btn_style = 'btn-success';
								$icon_complete ='fa-check';
								$action = 'Done';
							}
						@endphp
						<span>{{ $task->name }}</span>
						@if( Auth::user()->hasRole($task->role->name) )
						<a href="{{ route($task->route, ['singer' => $singer, 'task' => $task]) }}" class="link-confirm progress--link btn btn-sm force-xs {{$btn_style}}">
							<i class="fa fa-fw  {{$icon_complete}}"></i> {{$action}}
						</a>
						@endif
					
						@break
					@endif
				@endforeach
			@endif
		</div>
		<div class="r-table__cell singer-col--actions">

			@if ( Auth::user()->hasRole('Membership Team') )
			<div class="dropdown">
				<button class="btn btn-secondary btn-sm force-xs btn-block dropdown-toggle" type="button" id="moveDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Move to
				</button>
				<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					@foreach($singer_categories as $id => $category)
					<a class="dropdown-item " href="{{ route( 'singer.move', ['singer' => $singer, 'move_category' => $id] ) }}">{{ $category }}</a>
					@endforeach
				</div>
			</div>
			@endif
		</div>

		<div class="r-table__cell col--delete">
			@if ( Auth::user()->hasRole('Membership Team') )
				<a href="{{route( 'singer.delete', ['singer' => $singer] )}}" class="link-confirm text-danger"><i class="fa fa-fw fa-trash"></i></a>
			@endif
		</div>

	</div>
	
