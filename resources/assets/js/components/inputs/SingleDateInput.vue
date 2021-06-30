<template>
  <date-input
      :label="label"
      :input-name="inputName"
      :output-name="outputName"
      v-model="value"
  />
</template>

<script>

export default {
	name: 'SingleDateInput',
	props: {
		label: {
			type: String,
			required: true,
		},
		inputName: String,
		outputName: {
			type: String,
			required: true,
		},
		initValue: {
			type: String,
		},
	},
	data() {
		return {
      value: this.initValue ? new Date(this.initValue) : new Date(),
		};
	},
};
</script>

<style scoped>
div >>> .form-control {
	padding-right: 30px !important;
}
</style>
