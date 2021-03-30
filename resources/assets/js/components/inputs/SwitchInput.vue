<template>
    <div>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" :id="name" :name="name" :value="value" v-model="checked" @change='$emit("change", $event.target.checked)'>
            <label class="custom-control-label" :for="name">{{ label }}</label>
        </div>
        <p v-if="helpText">
            <small class="text-muted">{{ helpText }}</small>
        </p>
    </div>
</template>

<script>
export default {
    name: "SwitchInput",
    model: {
        prop: 'checked',
        event: 'change'
    },
    props: {
        label: String,
        name: String,
        value: String,
        checked: Boolean,
        helpText: String,
    }
}
</script>

<style scoped>

</style>