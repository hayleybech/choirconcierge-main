<div class="col-md-3">
	<?php
		// Prep singer details
		$singer_name = ( isset($singer['custom_fields']['Name']) ) ? $singer['custom_fields']['Name'] : 'Name Unknown';
		$singer_part = ( isset($singer['custom_fields']['Voice_Part']) ) ? $singer['custom_fields']['Voice_Part'] : 'No Voice Part';
		$singer_email = $singer['email'];
		
		$actions = array();
		// Prep profile action
		$profile_action = array(
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
		
		// Prep placement action
		$placement_action = array(
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
	
		// Prep fees action
		$fees_action = array(
			'link' => '#',
			'link_target' => '_self',
			'disabled' => 'disabled',
			'badge_class' => 'badge-secondary',
			'badge_text' => 'Coming Soon',
			'status_icon' => 'fa-square',
		);
		$actions['Fees Paid'] = $fees_action;
	
		//include('templates/singer_card.tpl.php');
	?> 
	<div class="card">
		<div class="card-body">
			<h4 class="card-title singer-name"><?php echo ( isset($singer_name) ) ? $singer_name : 'Name Unknown'; ?></h4>
			<h6 class="card-subtitle mb-2 text-muted singer-email"><?php echo $singer_email; ?></h6>
			<p class="card-text singer-part"><?php echo ( isset($singer_part) ) ? $singer_part : 'No Voice Part'; ?></p>
		</div>

		<div class="list-group list-group-flush">
		
			<?php foreach( $actions as $action_name => $action ): ?>
			<a href="<?php echo $action['link'] ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $action['disabled'] ?>" target="_blank">
				<div class="d-flex w-100 justify-content-between">
					<span><i class="fa fa-fw <?php echo $action['status_icon']; ?>"></i> <?php echo $action_name; ?></span>
					<small><?php echo $action['badge_text'] ?></small>
				</div>
			</a>
			<?php endforeach ?>

		</div>

	</div>
	
</div>