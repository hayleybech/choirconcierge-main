	<template>

    <div v-if="editing">
        <form :action="action" method="post">
            <input name="_method" type="hidden" value="PUT">
            <input name="_token" type="hidden" :value="csrf">

            <slot></slot>

            <button type="submit" :class="'btn btn-secondary ' + btnSize"><i class="far fa-fw fa-check"></i> Save</button>
        </form>
    </div>
    <div v-else>
        {{ value }} <button type="button" :class="'btn btn-link ' + btnSize" v-on:click="editing = true;"><i class="far fa-fw fa-edit"></i> {{ editLabel }}</button>
    </div>


</template>

<script>
export default {
    name: "InlineEditField",
    props: {
        action: {
            type: String,
            required: true
        },
        value: {
            type: String,
            required: true
        },
        csrf: {
            type: String,
            required: true
        },
        editLabel: {
            type: String,
            default: 'Edit'
        },
        smallButtons: {
            type: Boolean,
            default: false
        }
    },
    computed: {
      btnSize() {
        return this.smallButtons ? 'btn-sm' : '';
      }
    },
    data() {
        return {
            editing: false
        }
    }
}
</script>

<style scoped>

</style>