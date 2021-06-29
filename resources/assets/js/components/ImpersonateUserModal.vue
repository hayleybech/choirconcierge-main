<template>
	<div
		class="modal fade"
		id="impersonateUserModal"
		tabindex="-1"
		role="dialog"
		aria-labelledby="impersonateUserModalLabel"
		aria-hidden="true"
	>
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="impersonateUserModalLabel">Choose a user to impersonate</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>

				<div class="modal-body">
					<p>
						With this feature, you can instantly log in as another user to use the site through their eyes.
						This is a great way to test the functionality and security of the site. Note that any changes
						you make as that user will be permanent, just as if that user had changed it.
					</p>
					<p>
						You can return to your account at anytime by opening the account menu then clicking "Stop
						Impersonating".
					</p>
					<label for="impersonate_users">Users</label>
					<div class="input-group">
						<div class="input-group-prepend">
							<span class="input-group-text"><i class="fa fa-fw fa-user"></i></span>
						</div>
						<select
							v-model="userId"
							v-select2
							id="impersonate_users"
							name="impersonate_users[]"
							class="select2 form-control"
							data-model="users"
						></select>
					</div>
				</div>

				<div class="modal-footer">
					<form :action="action" method="get" class="d-inline-block">
						<button type="submit" class="btn btn-primary"><i class="far fa-fw fa-check"></i> Start</button>
						<button type="button" class="btn btn-link text-danger" data-dismiss="modal">Cancel</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'ImpersonateUserModal',
	props: {
		route: {
			type: String,
			required: true,
		},
	},
	computed: {
		action() {
			return this.route.replace('--replace--', this.userId);
		},
	},
	data() {
		return {
			userId: null,
		};
	},
};
</script>

<style scoped></style>
