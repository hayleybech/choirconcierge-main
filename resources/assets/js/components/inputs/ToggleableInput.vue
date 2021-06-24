<template>
	<div>
		<switch-input
			:label="label"
			:name="name"
			:value="value"
			v-model="isOpen"
			class="mb-4"
			:help-text="helpText"
		></switch-input>
		<div v-if="isOpen">
			<slot></slot>
		</div>
	</div>
</template>

<script>
export default {
	name: 'ToggleableInput',
	props: {
		name: {
			type: String,
			required: true,
		},
		label: {
			type: String,
			required: true,
		},
		value: {
			type: String,
			default: '1',
		},
		startOpen: {
			type: Boolean,
			default: false,
		},
		helpText: String,
	},
	data() {
		return {
			isOpen: this.startOpen,
		};
	},
};
</script>

<style scoped></style>
