<?php
// Store CSS badge classes for categories
$category_class = [
	'Prospects'             => 'text-primary',
	'Archived Prospects'    => 'text-warning',
	'Members'               => 'text-success',
	'Archived Members'      => 'text-danger',
];
?>
<tr class="row--singer">
	<td class="col--title">
		@can('view', $singer)
		<a href="{{route('singers.show', ['singer' => $singer])}}">
			<img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->name }}" class="user-avatar" width="50" height="50">
		</a>
		@else
		<img src="{{ $singer->user->getAvatarUrl('thumb') }}" alt="{{ $singer->name }}" class="user-avatar" width="50" height="50">
		@endcan
		<div class="item-title-wrapper">
			@can('view', $singer)
			<a href="{{ route('singers.show', ['singer' => $singer]) }}">
				{{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
			</a>
			@else
			{{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
			@endcan
			<small class="text-muted">{{ $singer->email }}</small>
			<small class="text-muted">{{ ( isset($singer->profile->phone) && $singer->profile->phone !== '' ) ? $singer->profile->phone : 'No phone' }}</small>
		</div>
	</td>
	<td class="col--part">
		<span class="badge badge-pill badge-light" {!! ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? 'style="background-color: '.$singer->voice_part->colour.';"' : '' !!}>
			<span class="d-md-none">{{ substr( $singer->voice_part->title ?? 'None', 0, 5 ) }}</span>
			<span class="d-none d-md-inline">{{ ( isset($singer->voice_part) && $singer->voice_part !== '' ) ? $singer->voice_part->title : 'No part' }}</span>
		</span>
	</td>
	@if($col_category)
	<td class="col--category">
		<span class="singer-category {{ $category_class[$singer->category->name] }}"><i class="fas fa-fw fa-circle"></i><span class="status__title ml-2">{{ $singer->category->name }}</span></span>
	</td>
	@endif
	@if($col_progress)
	<td class="col--progress">
		<!--<div class="progress">
			<div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
		</div>-->
		@if( $singer->onboarding_enabled && Auth::user()->can('viewAny', \App\Models\Task::class) )
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
					@if( Auth::user()->hasRole($task->role->name) )
					<a href="{{ route($task->route, ['singer' => $singer, 'task' => $task]) }}" class="link-confirm progress--link btn btn-sm force-xs mr-2 {{$btn_style}}">
						<i class="fa fa-fw  {{$icon_complete}}"></i> {{$action}}
					</a>
					@endif
					<span>{{ $task->name }}</span>

					@break
				@endif
			@endforeach
		@endif
	</td>
	@endif
	<td class="col--actions">

		@can('update', $singer)
		<div class="dropdown">
			<button class="btn btn-secondary btn-sm force-xs dropdown-toggle" type="button" id="moveDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Move to
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
				@foreach($singer_categories as $id => $category)
				<a class="dropdown-item " href="{{ route( 'singers.categories.update', ['singer' => $singer, 'move_category' => $id] ) }}">{{ $category }}</a>
				@endforeach
			</div>
		</div>
		@endcan
	</td>

	<td class="col--delete">
		@can('delete', $singer)
			<x-delete-button :action="route( 'singers.destroy', ['singer' => $singer] )"/>
		@endcan
	</td>

</tr>