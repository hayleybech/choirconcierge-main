<template>
	<div>
		<button
			type="button"
			class="btn btn-link text-danger p-0"
			data-toggle="modal"
			data-target="#repeatingEventDeleteModal"
			v-on:click="this.show"
		>
			<i class="far fa-fw fa-trash-alt"></i>
		</button>
	</div>
</template>

<script>
export default {
	name: 'RepeatingEventDeleteButton',
	props: {
		route: {
			type: String,
			required: true,
		},
		eventId: {
			type: String,
			required: true,
		},
		eventTitle: {
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
		eventIsParent: {
			type: Boolean,
			required: true,
		},
	},
	methods: {
		show() {
			this.$root.$emit('repeating-event-delete-button-clicked', {
				route: this.route,
				eventId: this.eventId,
				eventTitle: this.eventTitle,
				eventInPast: this.eventInPast,
				parentInPast: this.parentInPast,
				eventIsParent: this.eventIsParent,
			});
		},
	},
};
</script>

<style scoped></style>
