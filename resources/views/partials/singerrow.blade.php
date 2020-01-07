
	<?php
		/*
		// Prep singer details
		$singer_part = ( isset($singer['custom_fields']['Voice_Part']) ) ? $singer['custom_fields']['Voice_Part'] : 'No Voice Part';
		$singer_phone = $singer['custom_fields']['Phone'];
		
		// Fix missing zero
		if( substr($singer_phone, 0, 1) != '0' &&
			substr($singer_phone, 0, 1) != '+') {
			$singer_phone = '0' . $singer_phone;	
		}
		
		$actions = array();
		
		if( Auth::user()->hasRole('Membership Team') ){
			// Prep profile action
			$profile_action = array(
				'text' => 'Member Profile',
				'link' => route( 'singer.memberprofile', ['singer' => $singer['email'] ] ),
				'link_target' => '_blank',
				'disabled' => '',
				'badge_class' => 'badge-secondary',
				'badge_text' => 'Start <i class="fa fa-external-link"></i>',
				'status_icon' => 'fa-square-o',
			);

			if( isset( $singer['custom_fields']['Name'] ) ){
				$profile_action['disabled'] = 'disabled';
				$profile_action['badge_class'] = 'badge-info';
				$profile_action['badge_text'] = '';
				$profile_action['status_icon'] = 'fa-check-square-o';
			}
			$actions['Member Profile'] = $profile_action;
		}
		
		if( Auth::user()->hasRole('Music Team') ){
			// Prep placement action
			$placement_action = array(
				'text' => 'Voice Placement',
				'link' => route( 'singer.voiceplacement', ['singer' => $singer['email'] ] ),
				'link_target' => '_blank',
				'disabled' => '',
				'badge_class' => 'badge-secondary',
				'badge_text' => 'Start <i class="fa fa-external-link"></i>',
				'status_icon' => 'fa-square-o',
			);

			if( isset( $singer['custom_fields']['Voice_Part'] ) ){
				$placement_action['disabled'] = 'disabled';
				$placement_action['badge_class'] = 'badge-info';
				$placement_action['badge_text'] = '';
				$placement_action['status_icon'] = 'fa-check-square-o';
			}
			$actions['Voice Placement'] = $placement_action;
			
			// Prep audition action
			$audition_action = array(
				'text' => 'Audition Passed',
				'link' => route( 'singer.audition.pass', ['singer' => $singer['email'] ] ),
				'link_target' => '_self',
				'disabled' => '',
				'badge_class' => 'badge-secondary',
				'badge_text' => 'Mark',
				'status_icon' => 'fa-square-o',
			);
		
			if( in_array( 'Passed Vocal Assessment', $singer['tags'] ) ){
				$audition_action['disabled'] = 'disabled';
				$audition_action['badge_class'] = 'badge-success';
				$audition_action['badge_text'] = '';
				$audition_action['status_icon'] = 'fa-check-square-o';
			}
			$actions['Audition Passed'] = $audition_action;
		
		}
	
		if( Auth::user()->hasRole('Accounts Team') ) {
			// Prep fees action
			$fees_action = array(
				'text' => 'Fees Paid',
				'link' => route( 'singer.fees.paid', ['singer' => $singer['email'] ] ),
				'link_target' => '_self',
				'disabled' => '',
				'badge_class' => 'badge-secondary',
				'badge_text' => 'Mark',
				'status_icon' => 'fa-square-o',
			);
			if( in_array( 'Membership Fees Paid', $singer['tags'] ) ){
				$fees_action['disabled'] = 'disabled';
				$fees_action['badge_class'] = 'badge-success';
				$fees_action['badge_text'] = '';
				$fees_action['status_icon'] = 'fa-check-square-o';
			}
			$actions['Fees Paid'] = $fees_action;
		
		}
		
		if( Auth::user()->hasRole('Uniforms Team') ) {
			// Prep uniform action
			$uniform_action = array(
				'text' => 'Uniform Provided',
				'link' => route( 'singer.uniform.provided', ['singer' => $singer['email'] ] ),
				'link_target' => '_self',
				'disabled' => '',
				'badge_class' => 'badge-secondary',
				'badge_text' => 'Mark',
				'status_icon' => 'fa-square-o',
			);
			if( in_array( 'Uniform Provided', $singer['tags'] ) ){
				$uniform_action['disabled'] = 'disabled';
				$uniform_action['badge_class'] = 'badge-success';
				$uniform_action['badge_text'] = '';
				$uniform_action['status_icon'] = 'fa-check-square-o';
			}
			$actions['Uniforms Provided'] = $uniform_action;
		
		}
		
		if( Auth::user()->hasRole('Membership Team') ){
			// Prep account action
			$account_action = array(
				'text' => 'Account Created',
				'link' => route( 'singer.account.created', ['singer' => $singer['email'] ] ),
				'link_target' => '_self',
				'disabled' => '',
				'badge_class' => 'badge-secondary',
				'badge_text' => 'Mark',
				'status_icon' => 'fa-square-o',
			);

			if( in_array( 'Account Created', $singer['tags'] ) ){
				$account_action['disabled'] = 'disabled';
				$account_action['badge_class'] = 'badge-success';
				$account_action['badge_text'] = '';
				$account_action['status_icon'] = 'fa-check-square-o';
			}
			$actions['Account Created'] = $account_action;
		}
		*/

		/*
		if( Auth::user()->hasRole('Membership Team') ){
			// Prep administrative settings
			$operations = [];

			if( $singer->category->name == 'Prospects' ){
				$operations[] = array(
					'title' => 'Archive',
					'link'  => route( 'singer.move.archive', ['singer' => $singer ] ),
					'class' => 'btn-danger',
				);
			}
		}*/
	
	?>
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
			<div class="singer-name">
				<a href="{{route('singers.show', ['singer' => $singer])}}">
					{{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
				</a>
			</div>
			<div class="card-subtitle text-muted singer-email">{{ $singer->email }}</div>
		</div>
        <div class="r-table__cell column--progress">
            <!--<div class="progress">
                <div class="progress-bar bg-success" role="progressbar" style="width: 25%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
            </div>-->
            <a href="#" class="link-confirm progress--link">
                <span><i class="fa fa-fw fa-square-o"></i> Member Profile</span>
            </a>
        </div>
        <div class="r-table__cell column--part">
            <span class="singer-part"><i class="fa fa-users"></i> {{ ( isset($singer->placement->voice_part) && $singer->placement->voice_part != '' ) ? $singer->placement->voice_part : 'No part' }}</span><br>
        </div>
		<div class="r-table__cell column--category">
			<span class="singer-category badge {{ $category_class[$singer->category->name] }}">{{ $singer->category->name }}</span>
		</div>
        <div class="r-table__cell column--phone">
            <span class="singer-phone"><i class="fa fa-phone"></i> {{ ( isset($singer->profile->phone) && $singer->profile->phone != '' ) ? $singer->profile->phone : 'No phone' }}</span><br>
        </div>
        <div class="r-table__cell column--age">
            <span class="singer-age"><i class="fa fa-calendar-o"></i> {{ ( $singer->getAge() ) ? 'Age '.$singer->getAge() : 'No DOB' }}</span>
        </div>
        @if ( Auth::user()->hasRole('Membership Team') )
        <div class="r-table__cell column--actions">

            <form method="get" action="{{route( 'singer.move', ['singer' => $singer])}}" class="form-inline">
                <div class="input-group input-group-sm">
                    @php
                echo Form::select('move_category', $categories_move,
                0, ['class' => 'custom-select form-control-sm force-xs']);
                    @endphp

                    <div class="input-group-append">
                        <input type="submit" value="Move" class="btn btn-secondary btn-sm force-xs">
                    </div>
                </div>
            </form>
        </div>
        @endif

        <!--
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

		</div>-->


		{{--@if (count($operations) === 1)
		<div class="card-footer">
			@foreach( $operations as $op)
				<a href="{{ $op['link'] }}" class="btn btn-sm {{ $op['class'] }}">{{ $op['title'] }}</a>
			@endforeach
		</div>
		@endif--}}

		@if ( Auth::user()->hasRole('Membership Team') )
		<!--<div class="card-footer">

			<form method="get" action="{{route( 'singer.move', ['singer' => $singer])}}" class="form-inline">
				<div class="input-group input-group-sm">
					@php
						echo Form::select('move_category', $categories_move,
						0, ['class' => 'custom-select form-control-sm']);
					@endphp

					<div class="input-group-append">
						<input type="submit" value="Move" class="btn btn-secondary btn-sm">
					</div>
				</div>
			</form>

		</div>-->
		@endif
	</div>
	
