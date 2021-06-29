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
            :default-value="defaultCallTime"
            :disabled-time="disabledCallTime"
            @change="changeCallTime"
            :click-to-edit="true"
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
            :click-to-edit="true"
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
          :click-to-edit="true"
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
			callTime: this.initCallTime ? new Date(this.initCallTime) : new Date(),
			rawFormat: 'YYYY-MM-DD HH:mm:ss',
			defaultCallTime: this.initCallTime ? new Date(this.initCallTime) : new Date(),
			defaultStartDate: this.initStartDate ? new Date(this.initStartDate) : new Date(),
			defaultEndDate: this.initEndDate ? new Date(this.initEndDate) : new Date(),
		};
	},
	computed: {
		hasDescription() {
			return !!this.$slots['description'];
		},
	},
  created() {
    this.constrain();
  },
	methods: {
		/*
		 * Merge dates and times on edit
		 * Since we've opted for separate date and time controls,
		 * we need to copy the changes in both directions.
		 */
    changeStartDate(newStartDate) {
      this.startDate = this.setDate(newStartDate, this.startDate);
      this.callTime =  this.setDate(newStartDate, this.callTime);
      this.constrain();
    },
    changeEndDate(newEndDate) {
      this.endDate = this.setDate(newEndDate, this.endDate);
      this.constrain();
    },
		changeStartTime(newStartTime) {
			this.startDate = this.setTime(newStartTime, this.startDate);
			this.constrain();
		},
		changeEndTime(newEndTime) {
			this.endDate = this.setTime(newEndTime, this.endDate)
			this.constrain();
		},
		changeCallTime(newCallTime) {
			this.callTime = this.setTime(newCallTime, this.callTime);
			this.constrain();
		},
		constrain() {
      this.constrainCallTime();
      this.constrainEndTime();
		},
    constrainCallTime()
    {
      // callTime must be at least x earlier than startDate
      const minCallTimeBeforeStartTime = { minutes: 15}
      if (
          moment(this.callTime)
              .add(minCallTimeBeforeStartTime)
              .isSameOrAfter(this.startDate)
      ) {
        this.callTime = moment(this.startDate)
            .subtract(minCallTimeBeforeStartTime)
            .toDate();
        this.defaultCallTime = this.callTime;
      }
    },
    constrainEndTime()
    {
      // endDate must be at least y later than startDate
      const minEndDateAfterStartDate = { minutes: 15 };
      if (
          moment(this.startDate)
              .add(minEndDateAfterStartDate)
              .isSameOrAfter(this.endDate)
      ) {
        this.endDate = moment(this.startDate)
            .add(minEndDateAfterStartDate)
            .toDate();
        this.defaultEndDate = this.endDate;
      }
    },
		disabledCallTime(date) {
			return date >= this.startDate;
		},
		disabledEndTime(date) {
			return date <= this.startDate;
		},
    setTime(source, target)
    {
      const sourceMoment = moment(source);
      return moment(target)
          .set({
            hour: sourceMoment.hour(),
            minute: sourceMoment.minute(),
          })
          .toDate();
    },
    setDate(source, target)
    {
      const sourceMoment = moment(source);
      return moment(target)
          .set({
            year: sourceMoment.year(),
            month: sourceMoment.month(),
            date: sourceMoment.date(),
          })
          .toDate();
    }
	},
};
</script>

<style scoped></style>
