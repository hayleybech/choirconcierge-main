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
        startOpen: {
        	type: Boolean,
	        default: false,
        }
    },
	data() {
    	return {
    		isOpen: this.startOpen,
	    }
	}
}
</script>

<style scoped>

</style>