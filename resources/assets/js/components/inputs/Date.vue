<template>
    <div>
        <label :for="inputName">{{ label }}</label>

        <date-picker
            v-model="time"
            type="date"
            :format="displayFormat"
            :input-attr="{ name: inputName }"
            style="width:100%"
        ></date-picker>

        <input type="hidden" :name="outputName" :value="outputTime">

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
    name: "Date",
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
    },
    data() {
        return {
            time: this.value ? new Date(this.value) : new Date,
            rawFormat: 'YYYY-MM-DD HH:mm:ss',
            displayFormat: 'MMMM D, YYYY',
        }
    },
    computed: {
        outputTime() {
            return moment(this.time).format(this.rawFormat);
        }
    }
}
</script>

<style scoped>

</style>