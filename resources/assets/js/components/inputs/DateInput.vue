<template>
	<div>
		<label :for="inputName">{{ label }}</label>

		<date-picker
			v-model="time"
			type="date"
			:format="displayFormat"
			:input-attr="{ name: inputName }"
			:input-class="inputClass"
			style="width:100%"
		></date-picker>

		<input type="hidden" :name="outputName" :value="outputTime" />

		<p v-if="hasHelpText">
			<small class="text-muted">
				<slot name="help"></slot>
			</small>
		</p>
	</div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
export default {
	components: { DatePicker },
	name: 'DateInput',
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
		value: {
			type: String,
			default: null,
		},
		small: Boolean,
	},
	data() {
		return {
			time: this.value ? new Date(this.value) : new Date(),
			rawFormat: 'YYYY-MM-DD HH:mm:ss',
			displayFormat: 'MMMM D, YYYY',
		};
	},
	computed: {
		outputTime() {
			return moment(this.time).format(this.rawFormat);
		},
		hasHelpText() {
			return !!this.$slots['helpText'];
		},
		inputClass() {
			return this.small ? 'form-control form-control-sm' : 'form-control';
		},
	},
};
</script>

<style scoped>
div >>> .form-control {
	padding-right: 30px !important;
}
</style>
