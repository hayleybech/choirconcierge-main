<template>
    <div class="list-group-flush">
        <track-list-item v-for="attachment in attachments" :attachment="attachment" :song="song" :key="attachment.id"></track-list-item>
    </div>
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
            }
        }
    }
</script>

<style scoped>

</style>