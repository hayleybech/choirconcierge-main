<template>
    <a :href="href" v-on:click="confirmNavigate($event)">
        <slot></slot>
    </a>
</template>

<script>
export default {
    name: "LinkConfirm",
    props: {
        href: {
            type: String,
            required: true
        },
        description: String,
    },
    methods: {
        confirmNavigate(e) {
            const prompt = 'Are you sure?';
            const message = this.description ? 'Action: '+this.description : '';
            if( ! confirm( prompt+' '+message ) ) {
                e.preventDefault();
            }
        }
    }
}
</script>

<style scoped>

</style>