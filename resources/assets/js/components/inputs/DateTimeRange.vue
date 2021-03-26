<template>
    <div>
        <label :for="inputName">{{ label }}</label>

        <date-picker
            v-model="time"
            range
            :type="type"
            :use12h="true"
            :show-second="false"
            :format="displayFormat"
            :input-class="inputClass"
            :input-attr="{ name: inputName }"
            style="width:100%"
            :shortcuts="shortcuts"
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

        <input type="hidden" :name="startName" :value="startTime">
        <input type="hidden" :name="endName" :value="endTime">

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
    name: "DateTimeRange",
    props: {
        label: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: 'datetime' // datetime || date || time
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
        },
        showShortcuts: {
            type: Boolean,
            default: false
        },
        small: Boolean,
    },
    data() {
        return {
            time: [
                this.startValue ? new Date(this.startValue) : new Date,
                this.endValue ? new Date(this.endValue) : new Date
            ],
            rawFormat: 'YYYY-MM-DD HH:mm:ss',
        }
    },
    computed: {
        displayFormat() {
            const formats = {
                date: 'YYYY-MM-DD',
                time: 'hh:mm A',
                datetime: 'MMMM D, YYYY hh:mm A'
            }
            return formats[this.type];
        },
        startTime() {
            return moment(this.time[0]).format(this.rawFormat);
        },
        endTime() {
            return moment(this.time[1]).format(this.rawFormat);
        },
        shortcuts() {
            if(!this.showShortcuts){
                return [];
            }
            const ranges = {
                'Today': [moment(), moment()],
                'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            };

            const that = this;

            return Object.values(
                this.objectMap(ranges, (dates, name) => {
                    return {
                        text: name,
                        onClick: () => {
                            that.time = [
                                dates[0].toDate(),
                                dates[1].toDate()
                            ]
                        }
                    }
                })
            );
        },
        hasHelpText () {
            return !!this.$slots['helpText'];
        },
        inputClass() {
            return this.small ? 'form-control form-control-sm' : 'form-control';
        },
    },
    methods: {
        // Why oh why can't JS have PHP-style assoc arrays? :'(
        // @see https://stackoverflow.com/a/14810722/563974
        // @todo move objectMap to somewhere more re-usable
        // returns a new object with the values at each key mapped using fn(value)
        objectMap(obj, fn) {
            return Object.fromEntries(
                Object.entries(obj).map(
                    ([k, v], i) => [k, fn(v, k, i)]
                )
            );
        },
    }
}
</script>

<style scoped>
div >>> .form-control {
    padding-right: 30px !important;
}
</style>