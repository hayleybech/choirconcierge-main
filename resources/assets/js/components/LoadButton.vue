<template>
    <button type="submit" :class="'btn '+theme+' '+size+''" v-on:click="setLoading" :disabled="disabled">
        <i :class="'fa-fw '+currentIcon"></i> {{ currentLabel }}
    </button>
</template>

<script>
export default {
    name: "LoadButton",
    props: {
        theme: {
            type: String,
            default: 'btn-primary'
        },
        size: {
            type: String,
            default: ''
        },
        icon: {
            type: String,
            default: 'far fa-check'
        },
        iconLoading: {
            type: String,
            default: 'fad fa-spinner-third fa-spin'
        },
        label: {
            type: String,
            default: 'Submit'
        },
        labelLoading: {
            type: String,
            default: 'Loading...'
        }
    },
    data() {
        return {
            currentIcon: this.icon,
            currentLabel: this.label,
            disabled: false
        }
    },
    methods: {
        setLoading()
        {
            if( this.$el.form.checkValidity() ){
                this.disabled = true;
                this.currentIcon = this.iconLoading;
                this.currentLabel = this.labelLoading;
                this.$el.form.submit();
            }
        }
    }
}
</script>

<style scoped>

</style>