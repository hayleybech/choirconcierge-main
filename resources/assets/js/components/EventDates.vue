<template>
	<div>
		<div class="row mb-2">
			<div class="col-8">
				<input-datetime-range label="Event Date" type="date" input-name="date_range" start-name="start_date" end-name="end_date" :start-value="startDate" :end-value="endDate"></input-datetime-range>
			</div>
			<div class="col-md-4">
				<input-datetime label="Call Time" type="time" input-name="call_time_input" output-name="call_time" :value="callTime"></input-datetime>
			</div>
		</div>

		<div class="row mb-2">
			<div class="col-md-6">
				<input-datetime label="Onstage Time" :optional="true" type="time" input-name="call_time_input" output-name="call_time" :value="callTime"></input-datetime>
			</div>
			<div class="col-md-6">
				<input-datetime label="End Time" :optional="true" type="time" input-name="call_time_input" output-name="call_time" :value="callTime"></input-datetime>
			</div>
		</div>

		<p v-if="hasDescription">
			<small class="text-muted">
				<slot name="description"></slot>
			</small>
		</p>
	</div>
</template>

<script>
export default {
	name: "EventDates",
	props: {
		startDate: String,
		endDate: String,
		callTime: String
	},
	computed: {
		hasDescription () {
			return !!this.$slots['description'];
		},
	}
}
</script>

<style scoped>

</style>