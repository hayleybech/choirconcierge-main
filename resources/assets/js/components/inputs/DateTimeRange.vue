<template>
    <div>
        <label :for="inputName">{{ label }}</label>

        <date-picker
            v-model="time"
            range
            type="datetime"
            :use12h="true"
            :show-second="false"
            :format="displayFormat"
            :input-attr="{ name: inputName }"
            style="width:100%"
        ></date-picker>

        <input type="hidden" :name="startName" :value="startTime">
        <input type="hidden" :name="endName" :value="endTime">

        <p>
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
    name: "DateTimeRange",
    props: {
        label: {
            type: String,
            required: true
        },
        inputName: String,
        startName: {
            type: String,
            required: true,
        },
        endName: {
            type: String,
            required: true,
        },
        startValue: {
            type: String,
            default: null
        },
        endValue: {
            type: String,
            default: null
        }
    },
    data() {
        return {
            time: [
                this.startValue ? new Date(this.startValue) : new Date,
                this.endValue ? new Date(this.endValue) : new Date
            ],
            rawFormat: 'YYYY-MM-DD HH:mm:ss',
            displayFormat: 'MMMM D, YYYY hh:mm A',
        }
    },
    computed: {
        startTime() {
            return moment(this.time[0]).format(this.rawFormat);
        },
        endTime() {
            return moment(this.time[1]).format(this.rawFormat);
        }
    }
}
</script>

<style scoped>

</style>