<template>
	<div>
    <div class="row mb-2">
      <div class="col-md-6">
        <date-input
					label="Start Date"
					type="date"
					input-name="start_date"
          output-name="range_start_date"
					v-model="startDate"
					@change="changeStartDate"
				/>
			</div>
      <div class="col-md-3">
        <datetime-input
            label="Onstage Time"
            type="time"
            input-name="start_date_input"
            output-name="start_date"
            v-model="startDate"
            :default-value="defaultStartDate"
            :disabled-time="disabledStartTime"
            @change="changeStartTime"
        />
      </div>
      <div class="col-md-3">
        <datetime-input
            label="Call Time"
            type="time"
            input-name="call_time_input"
            output-name="call_time"
            v-model="callTime"
            @change="changeCallTime"
        />
      </div>

		</div>

		<div class="row mb-2">
      <div class="col-md-6">
        <date-input
            label="End Date"
            type="date"
            input-name="end_date"
            output-name="range_end_date"
            v-model="endDate"
            @change="changeEndDate"
        />
      </div>
			<div class="col-md-3">
				<datetime-input
					label="End Time"
					type="time"
					input-name="end_date_input"
					output-name="end_date"
					v-model="endDate"
					:default-value="defaultEndDate"
					:disabled-time="disabledEndTime"
					@change="changeEndTime"
				/>
			</div>
		</div>

		<p v-if="hasDescription">
			<small class="text-muted">
				<slot name="description" />
			</small>
		</p>
	</div>
</template>

<script>
import moment from 'moment';

export default {
	name: 'EventDates',
	props: {
		initStartDate: String,
		initEndDate: String,
		initCallTime: String,
	},
	data() {
		return {
			startDate: this.initStartDate ? new Date(this.initStartDate) : new Date(),
			endDate: this.initEndDate ? new Date(this.initEndDate) : new Date(),
			callTime: this.initEndDate ? new Date(this.initCallTime) : new Date(),
			rawFormat: 'YYYY-MM-DD HH:mm:ss',
			defaultStartDate: this.initStartDate ? new Date(this.initStartDate) : new Date(),
			defaultEndDate: this.initEndDate ? new Date(this.initEndDate) : new Date(),
		};
	},
	computed: {
		hasDescription() {
			return !!this.$slots['description'];
		},
	},
	methods: {
		/*
		 * Merge dates and times on edit
		 * Since we've opted for separate date and time controls,
		 * we need to copy the changes in both directions.
		 */
    changeStartDate(date) {
      const newStartDate = moment(date);

      this.startDate = moment(this.startDate)
          .set({
            year: newStartDate.year(),
            month: newStartDate.month(),
            date: newStartDate.date(),
          })
          .toDate();

      this.callTime = moment(this.callTime)
          .set({
            year: newStartDate.year(),
            month: newStartDate.month(),
            date: newStartDate.date(),
          })
          .toDate();

      this.updateDefaults();
    },
    changeEndDate(date) {
      const newEndDate = moment(date);

      this.endDate = moment(this.endDate)
          .set({
            year: newEndDate.year(),
            month: newEndDate.month(),
            date: newEndDate.date(),
          })
          .toDate();

      this.updateDefaults();
    },
		changeStartTime(date) {
			const newStartDate = moment(date);
			this.startDate = moment(this.startDate)
				.set({
					hour: newStartDate.hour(),
					minute: newStartDate.minute(),
				})
				.toDate();

			this.updateDefaults();
		},
		changeEndTime(date) {
			const newEndDate = moment(date);
			this.endDate = moment(this.endDate)
				.set({
					hour: newEndDate.hour(),
					minute: newEndDate.minute(),
				})
				.toDate();

			this.updateDefaults();
		},
		changeCallTime(date) {
			const newCall = moment(date);
			this.callTime = moment(this.callTime)
				.set({
					hour: newCall.hour(),
					minute: newCall.minute(),
				})
				.toDate();

			this.updateDefaults();
		},
		updateDefaults() {
			// startDate must be at least x later than callTime
			const minAfterCallTime = { minutes: 15 };
			if (
				moment(this.callTime)
					.add(minAfterCallTime)
					.isSameOrAfter(this.startDate)
			) {
				this.startDate = moment(this.callTime)
					.add(minAfterCallTime)
					.toDate();
				this.defaultStartDate = this.startDate;
			}
			// endDate must be at least y later than startDate
			const minAfterStartDate = { minutes: 15 };
			if (
				moment(this.startDate)
					.add(minAfterStartDate)
					.isSameOrAfter(this.endDate)
			) {
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
		},
	},
};
</script>

<style scoped></style>
