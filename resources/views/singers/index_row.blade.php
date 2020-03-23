	<?php
	// Store CSS badge classes for categories
	$category_class = array(
		'Prospects'             => 'badge-primary',
		'Archived Prospects'    => 'badge-danger',
		'Members'               => 'badge-success',
		'Archived Members'      => 'badge-warning',
	);
	?>
	<div class="r-table__row">
        <div class="r-table__cell column--mark">
            <input type="checkbox" />
        </div>
		<div class="r-table__cell column--singer">
			<a class="item-title" href="{{route('singers.show', ['singer' => $singer])}}">
				{{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
			</a>
			<div class="text-muted singer-email">{{ $singer->email }}</div>
		</div>
        <div class="r-table__cell column--progress">
            <!--<div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>-->
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
					<a href="{{ route($task->route, ['singer' => $singer, 'task' => $task]) }}" class="link-confirm progress--link btn btn-sm force-xs {{$btn_style}}">
						<i class="fa fa-fw  {{$icon_complete}}"></i> {{$action}}
					</a>
					@break
				@endif
			@endforeach
        </div>
        <div class="r-table__cell column--part">
            <span class="singer-part"><i class="fa fa-users"></i> {{ ( isset($singer->placement->voice_part) && $singer->placement->voice_part !== '' ) ? $singer->placement->voice_part : 'No part' }}</span><br>
        </div>
		<div class="r-table__cell column--category">
			<span class="singer-category badge badge-pill {{ $category_class[$singer->category->name] }}">{{ $singer->category->name }}</span>
		</div>
        <div class="r-table__cell column--phone">
            <span class="singer-phone"><i class="fa fa-phone-alt"></i> {{ ( isset($singer->profile->phone) && $singer->profile->phone !== '' ) ? $singer->profile->phone : 'No phone' }}</span><br>
        </div>
        <div class="r-table__cell column--actions">

			@if ( Auth::user()->hasRole('Membership Team') )
			<div class="dropdown">
				<button class="btn btn-secondary btn-sm force-xs dropdown-toggle" type="button" id="moveDropdownButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
					Move to
				</button>
				<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
					@foreach($categories_move as $id => $category)
					<a class="dropdown-item " href="{{ route( 'singer.move', ['singer' => $singer, 'move_category' => $id] ) }}">{{ $category }}</a>
					@endforeach
				</div>
			</div>

			<a href="{{route( 'singer.delete', ['singer' => $singer] )}}" class="link-confirm btn btn-link text-danger btn-sm ml-2"><i class="fa fa-fw fa-times"></i></a>
			@endif
		</div>

	</div>
	
