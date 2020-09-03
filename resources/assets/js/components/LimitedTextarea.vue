<template xmlns="http://www.w3.org/1999/html">
    <div>
        <textarea :id="fieldId" :name="fieldName" class="form-control" :rows="rows" :maxlength="maxlength" v-on:keydown="updateCount">{{ value }}</textarea>
        <small class="form-text text-muted">
            {{ count }} / {{ maxlength }}
        </small>
    </div>
</template>

<script>
export default {
    name: "LimitedTextarea",
    props: {
        fieldId: {
            type: String,
            required: true
        },
        fieldName: {
            type: String,
            required: true
        },
        maxlength: {
            type: Number,
            required: true
        },
        value: {
            type: String,
            default: ''
        },
        rows: {
            type: Number,
            default: 3
        }
    },
    data() {
        return {
            count: this.value.length
        }
    },
    methods: {
        updateCount(e) {
            this.count = e.target.value.length;
        }
    }
}
</script>

<style scoped>

</style>