<div class="col-md-3">
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
		
		// Prep administrative settings
		$operations = [];
		
		if( in_array( 'Category - Prospective Member', $singer['tags'] ) ){
			$operations[] = array(
				'title' => 'Archive',
				'link'  => route( 'singer.move.archive', ['singer' => $singer['email'] ] ),
				'class' => 'btn-danger',
			);
		}*/
	
	?> 
	<div class="card">
		<div class="card-body">
			<h4 class="card-title singer-name">
				<a href="{{route('singers.show', ['singer' => $singer])}}">
					{{ ( isset($singer->name) ) ? $singer->name : 'Name Unknown' }}
				</a>
			</h4>
			<h6 class="card-subtitle mb-2 text-muted singer-email">{{ $singer->email }}</h6>
			<p class="card-text">
				<span class="singer-part"><i class="fas fa-user-friends"></i> {{ ( isset($singer_part) ) ? $singer_part : 'No Voice Part' }}</span><br>
				<span class="singer-phone"><i class="fa fa-phone"></i> {{ ( isset($singer_phone) ) ? $singer_phone : '' }}</span><br>
			</p>*/?>
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
		
		<?php /*
		@if (count($operations) === 1)
		<div class="card-footer">
			@foreach( $operations as $op)
			<a href="{{ $op['link'] }}" class="btn btn-sm {{ $op['class'] }}">{{ $op['title'] }}</a>
			@endforeach
		</div>
		@endif
		*/?>
	</div>
	
</div>