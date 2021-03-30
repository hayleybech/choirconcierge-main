<template>
	<div>
		<div class="row mb-2">
			<div class="col-md-12">
				<datetime-range-input label="Event Date" type="date" input-name="date_range" start-name="range_start_date" end-name="range_end_date" v-model="range" @change="changeDates"></datetime-range-input>
			</div>
		</div>

		<div class="row mb-2">
			<div class="col-md-6">
				<datetime-input label="Call Time" type="time" input-name="call_time_input" output-name="call_time" v-model="callTime" @change="changeCallTime"></datetime-input>
			</div>
			<div class="col-md-3">
				<datetime-input label="Onstage Time" type="time" input-name="start_date_input" output-name="start_date" v-model="startDate" :default-value="defaultStartDate" :disabled-time="disabledStartTime" @change="changeStartTime"></datetime-input>
			</div>
			<div class="col-md-3">
				<datetime-input label="End Time" type="time" input-name="end_date_input" output-name="end_date" v-model="endDate" :default-value="defaultEndDate" :disabled-time="disabledEndTime" @change="changeEndTime"></datetime-input>
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
import moment from "moment";

export default {
	name: "EventDates",
	props: {
		initStartDate: String,
		initEndDate: String,
		initCallTime: String
	},
	data() {
		return {
			range: [
				this.initStartDate ? new Date(this.initStartDate) : new Date,
				this.initEndDate ? new Date(this.initEndDate) : new Date
			],
			startDate: this.initStartDate ? new Date(this.initStartDate) : new Date,
			endDate: this.initEndDate ? new Date(this.initEndDate) : new Date,
			callTime: this.initEndDate ? new Date(this.initCallTime) : new Date,
			rawFormat: 'YYYY-MM-DD HH:mm:ss',
			defaultStartDate: this.initStartDate ? new Date(this.initStartDate) : new Date,
			defaultEndDate: this.initEndDate ? new Date(this.initEndDate) : new Date,
		}
	},
	computed: {
		hasDescription () {
			return !!this.$slots['description'];
		},
	},
	methods: {
		/**
		 * Merge dates and times on edit
		 * Since we've opted for separate date and time controls,
		 * we need to copy the changes in both directions.
		 * @param dates the incoming dates
		 */
		changeDates(dates){
			const newStartDate = moment(dates[0]);
			const newEndDate = moment(dates[1]);

			this.startDate = moment(this.startDate).set({
					year: newStartDate.year(),
					month: newStartDate.month(),
					date: newStartDate.date(),
				}).toDate();

			this.endDate = moment(this.endDate).set({
					year: newEndDate.year(),
					month: newEndDate.month(),
					date: newEndDate.date(),
				}).toDate();

			this.callTime = moment(this.callTime).set({
					year: newStartDate.year(),
					month: newStartDate.month(),
					date: newStartDate.date(),
				}).toDate();

			this.updateDefaults();
		},
		changeStartTime(date) {
			const newStartDate = moment(date);
			this.startDate = moment(this.startDate).set({
					hour: newStartDate.hour(),
					minute: newStartDate.minute(),
				}).toDate();

			this.updateDefaults();
		},
		changeEndTime(date) {
			const newEndDate = moment(date);
			this.endDate = moment(this.endDate).set({
					hour: newEndDate.hour(),
					minute: newEndDate.minute(),
				}).toDate();

			this.updateDefaults();
		},
		changeCallTime(date) {
			const newCall = moment(date);
			this.callTime = moment(this.callTime).set({
					hour: newCall.hour(),
					minute: newCall.minute(),
				}).toDate();

			this.updateDefaults();
		},
		updateDefaults() {
			// startDate must be at least x later than callTime
			const minAfterCallTime = {minutes: 15};
			if(moment(this.callTime).add(minAfterCallTime).isSameOrAfter(this.startDate)) {
				this.startDate = moment(this.callTime)
					.add(minAfterCallTime)
					.toDate();
				this.defaultStartDate = this.startDate;
			}
			// endDate must be at least y later than startDate
			const minAfterStartDate = {minutes: 15};
			if(moment(this.startDate).add(minAfterStartDate).isSameOrAfter(this.endDate)) {
				this.endDate = moment(this.startDate)
					.add(minAfterStartDate)
					.toDate();
				this.defaultEndDate = this.endDate;
			}
		},
		disabledStartTime(date) {
			return date <= this.callTime;
		},
		disabledEndTime(date) {
			return date <= this.startDate;
		}
	}
}
</script>

<style scoped>

</style>