<template>
	<div v-if="editing">
    <slot></slot>
	</div>
	<div v-else>
    <button v-on:click="editing = true" class="form-control-plaintext btn-link text-left">{{ value }}</button>

<!--    <small class="text-muted">-->
<!--      {{ editLabel }}-->
<!--    </small>-->
	</div>
</template>

<script>
export default {
	name: 'ClickToEditField',
	props: {
		value: {
			type: String,
			required: true,
		},
		editLabel: {
			type: String,
			default: 'Click to edit',
		},
		smallButtons: {
			type: Boolean,
			default: false,
		},
    enabled: {
		  type: Boolean,
      default: false,
    }
	},
	computed: {
		btnSize() {
			return this.smallButtons ? 'btn-sm' : '';
		},
	},
	data() {
		return {
			editing: !this.enabled,
		};
	},
};
</script>

<style scoped></style>
