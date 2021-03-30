<template>
    <div>
        <switch-input :label="label" :name="name" :value="value" v-model="isOpen"></switch-input>
        <div v-if="isOpen">
            <slot></slot>
        </div>
    </div>
</template>

<script>
export default {
    name: "ToggleableInput",
    props: {
        name: {
            type: String,
            required: true
        },
        label: {
            type: String,
            required: true
        },
        value: {
        	type: String,
            default: '1',
        },
        isOpen: Boolean,
    },
}
</script>

<style scoped>

</style>