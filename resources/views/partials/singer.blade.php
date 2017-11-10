<div class="col-md-3">
	<?php
		// Prep singer details
		$singer_name = ( isset($singer['custom_fields']['Name']) ) ? $singer['custom_fields']['Name'] : 'Name Unknown';
		$singer_part = ( isset($singer['custom_fields']['Voice_Part']) ) ? $singer['custom_fields']['Voice_Part'] : 'No Voice Part';
		$singer_email = $singer['email'];
		
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
				'link' => '#',
				'link_target' => '_self',
				'disabled' => 'disabled',
				'badge_class' => 'badge-secondary',
				'badge_text' => 'Coming Soon',
				'status_icon' => 'fa-square',
			);
			$actions['Fees Paid'] = $fees_action;
		
		}
	
	?> 
	<div class="card">
		<div class="card-body">
			<h4 class="card-title singer-name">{{ ( isset($singer_name) ) ? $singer_name : 'Name Unknown' }}</h4>
			<h6 class="card-subtitle mb-2 text-muted singer-email">{{ $singer_email }}</h6>
			<p class="card-text singer-part">{{ ( isset($singer_part) ) ? $singer_part : 'No Voice Part' }}</p>
		</div>

		<div class="list-group list-group-flush">
		
			@foreach( $actions as $action )
			<a href="{{ $action['link'] }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $action['disabled'] }}" target="_blank">
				<div class="d-flex w-100 justify-content-between">
					<span><i class="fa fa-fw {{ $action['status_icon'] }}"></i> {{ $action['text'] }}</span>
					<small>{!! $action['badge_text'] !!}</small>
				</div>
			</a>
			@endforeach

		</div>

	</div>
	
</div>