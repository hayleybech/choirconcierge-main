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