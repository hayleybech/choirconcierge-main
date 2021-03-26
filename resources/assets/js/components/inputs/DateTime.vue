<template>
    <div>
        <label :for="inputName">{{ label }}</label>

        <date-picker
            v-model="time"
            type="datetime"
            :use12h="true"
            :show-second="false"
            :minute-step="5"
            :format="displayFormat"
            :input-class="inputClass"
            :input-attr="{ name: inputName }"
            style="width:100%"
        >
            <!-- TEMPORARY: Add icon for time type. The lib dev has already committed this as the default in the upcoming release -->
            <template #icon-calendar v-if="'time' === type">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24px" height="24px">
                    <path d="M0 0h24v24H0z" fill="none" />
                    <path
                            d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"
                    />
                    <path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                </svg>
            </template>
        </date-picker>

        <input type="hidden" :name="outputName" :value="outputTime">

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
    name: "DateTime",
    props: {
        label: {
            type: String,
            required: true
        },
        inputName: String,
        outputName: {
            type: String,
            required: true,
        },
        value: {
            type: String,
            default: null
        },
        small: Boolean,
    },
    data() {
        return {
            time: this.value ? new Date(this.value) : new Date,
            rawFormat: 'YYYY-MM-DD HH:mm:ss',
            displayFormat: 'MMMM D, YYYY hh:mm A',
        }
    },
    computed: {
        outputTime() {
            return moment(this.time).format(this.rawFormat);
        },
        hasHelpText () {
            return !!this.$slots['helpText'];
        },
        inputClass() {
            return this.small ? 'form-control form-control-sm' : 'form-control';
        },
    }
}
</script>

<style scoped>
div >>> .form-control {
    padding-right: 30px !important;
}
</style>