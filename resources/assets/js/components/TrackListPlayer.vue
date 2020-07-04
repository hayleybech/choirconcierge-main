<template>
    <div class="track-list-player">
        <div class="card-body">
            <track-player :title="current_track.title" :src="current_track.download_url" ref="player"></track-player>
        </div>

        <track-list :song="song" :attachments="attachments" :current="current_track_index" v-on:play="play"></track-list>
    </div>
</template>

<script>
export default {
    name: "TrackListPlayer",
    props: {
        song: {
            required: true
        },
        attachments: {
            required: true,
            type: Array
        }
    },
    data() {
        return {
            current_track_index: 0 // key in attachment array, NOT attachment id
        }
    },
    computed: {
        current_track() {
            return this.attachments[this.current_track_index];
        }
    },
    methods: {
        play(index) {
            this.current_track_index = index;
        },
        pause() {
            this.$refs.player.pause();
        }
    }
}
</script>

<style scoped>

</style>