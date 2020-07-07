<template>
    <table class="table">
        <tbody>
            <track-list-item
                v-for="(attachment, index) in attachments"
                :attachment="attachment"
                :song="song"
                :key="attachment.id"
                :isCurrent="isCurrent(index)"
                v-on:play="$emit('play', index)"
                v-on:pause="$emit('pause')"
            ></track-list-item>
        </tbody>
    </table>
</template>

<script>
import TrackListItem from "./TrackListItem";
export default {
    name: "TrackList",
    components: {TrackListItem},
    props: {
        song: {
            required: true
        },
        attachments: {
            required: true,
            type: Array
        },
        current: {
            type: Number,
            required: true
        }
    },
    methods: {
        isCurrent(index) {
            return index === this.current;
        }
    }
}
</script>

<style scoped>

</style>