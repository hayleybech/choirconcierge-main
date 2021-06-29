<template>
	<div>
		<label :for="inputName">{{ label }}</label>

    <click-to-edit-field :enabled="clickToEdit" :value="displayTime">
      <date-picker
          v-bind:value="value"
          v-on:input="onInput"
          v-on:change="onChange"
          type="date"
          :format="displayFormat"
          :input-attr="{ name: inputName }"
          :input-class="inputClass"
          style="width:100%"
      />
    </click-to-edit-field>

		<input type="hidden" :name="outputName" :value="outputTime" />

		<p v-if="hasHelpText">
			<small class="text-muted">
				<slot name="help" />
			</small>
		</p>
	</div>
</template>

<script>
import moment from 'moment';
import DatePicker from 'vue2-datepicker';
import 'vue2-datepicker/index.css';
import ClickToEditField from "../ClickToEditField";
export default {
	components: {ClickToEditField, DatePicker },
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
			type: Date,
		},
		small: Boolean,
    clickToEdit: {
		  type: Boolean,
      default: false,
    },
	},
	data() {
		return {
			rawFormat: 'YYYY-MM-DD HH:mm:ss',
			displayFormat: 'MMMM D, YYYY',
		};
	},
	computed: {
		outputTime() {
			return moment(this.value).format(this.rawFormat);
		},
    displayTime() {
      return moment(this.value).format(this.displayFormat);
    },
		hasHelpText() {
			return !!this.$slots['helpText'];
		},
		inputClass() {
			return this.small ? 'form-control form-control-sm' : 'form-control';
		},
	},
  methods: {
    onInput(date, type) {
      this.$emit('input', date, type);
    },
    onChange(date, type) {
      this.$emit('change', date, type);
    },
  },
};
</script>

<style scoped>
div >>> .form-control {
	padding-right: 30px !important;
}
</style>
