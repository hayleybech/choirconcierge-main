<template>
	<div
		class="modal fade"
		id="repeatingEventEditModeModal"
		tabindex="-1"
		role="dialog"
		aria-labelledby="repeatingEventEditModeModalLabel"
		aria-hidden="true"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="repeatingEventEditModeModalLabel">Edit repeating event</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<fieldset class="form-group">
						<legend class="col-form-label">
							This is a repeating event. How would you like to edit it?
						</legend>

						<div
							id="edit_mode"
							class="btn-group-vertical btn-group-toggle d-flex bg-white"
							data-toggle="buttons"
						>
							<label
								for="edit_mode_single"
								class="btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center active"
							>
								<i class="far fa-fw fa-calendar-day fa-2x mr-3"></i>
								<span>
									<input
										id="edit_mode_single"
										name="edit_mode"
										value="single"
										type="radio"
										autocomplete="off"
										v-model="editMode"
									/>
									<span class="h5">Only this event</span>
									<span class="form-text">
										All other events in the series will remain the same.
									</span>
								</span>
							</label>

							<label
								for="edit_mode_following"
								:class="
									'btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center ' +
										followingModeDisabledClass
								"
							>
								<i class="far fa-fw fa-calendar-week fa-2x mr-3"></i>
								<span>
									<input
										id="edit_mode_following"
										name="edit_mode"
										value="following"
										type="radio"
										autocomplete="off"
										v-model="editMode"
										:disabled="followingModeDisabled"
									/>
									<span class="h5">Following events</span>
									<span class="form-text">
										This and all the following events will be changed.<br />
										<strong>Any changes to future events will be lost, including RSVPs.</strong>
									</span>
									<span class="form-text text-danger" v-if="eventInPast"
										>This option affects events in the past. To protect attendance data, you cannot
										bulk edit past events. Please edit individually instead.</span
									>
								</span>
							</label>

							<label
								for="edit_mode_all"
								:class="
									'btn btn-outline-dark btn-radio py-3 px-3 text-left d-flex align-items-center ' +
										allModeDisabledClass
								"
							>
								<i class="far fa-fw fa-calendar-alt fa-2x mr-3"></i>
								<span>
									<input
										id="edit_mode_all"
										name="edit_mode"
										value="all"
										type="radio"
										autocomplete="off"
										v-model="editMode"
										:disabled="allModeDisabled"
									/>
									<span class="h5">All events</span>
									<span class="form-text">
										All events in the series will be changed.<br />
										<strong
											>Any changes to other events will be lost, including RSVPs and attendance
											records.</strong
										>
									</span>
									<span class="form-text text-warning" v-if="!isParent && !parentInPast"
										>You will be redirected to the first event in the series to make these
										changes.</span
									>
									<span class="form-text text-danger" v-if="parentInPast"
										>This option affects events in the past. To protect attendance data, you cannot
										bulk edit past events. Please edit individually instead.</span
									>
								</span>
							</label>
						</div>
					</fieldset>
				</div>

				<div class="modal-footer">
					<a :href="url" class="btn btn-primary"><i class="far fa-fw fa-check"></i> Start</a>
					<button type="button" class="btn btn-link text-danger" data-dismiss="modal">Cancel</button>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'RepeatingEventEditModeModal',
	props: {
		route: {
			type: String,
			required: true,
		},
		eventInPast: {
			type: Boolean,
			required: true,
		},
		parentInPast: {
			type: Boolean,
			required: true,
		},
		isParent: {
			type: Boolean,
			required: true,
		},
	},
	data() {
		return {
			editMode: 'single',
		};
	},
	computed: {
		url() {
			let filteredEditMode = this.editMode;
			if (filteredEditMode === 'following' && this.isParent) {
				filteredEditMode = 'all';
			}
			return this.route.replace('--replace--', filteredEditMode);
		},
		followingModeDisabled() {
			// here, we're NOT disabling if the event is the parent, but as shown in the url() method above, we ARE simply swapping the URL to the 'all' one.
			return this.eventInPast;
		},
		followingModeDisabledClass() {
			return this.followingModeDisabled ? 'disabled' : '';
		},
		allModeDisabled() {
			return this.parentInPast;
		},
		allModeDisabledClass() {
			return this.allModeDisabled ? 'disabled' : '';
		},
	},
};
</script>

<style scoped></style>
