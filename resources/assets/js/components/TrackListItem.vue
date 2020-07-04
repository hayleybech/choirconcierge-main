<template>
    <div class="list-group-item d-flex justify-content-between">
        <div class="d-flex">
            <button v-if="isPlayable && ! playing" v-on:click="$emit('play')" class="btn btn-link btn-sm mr-2"><i class="fas fa-fw fa-play"></i></button>
            <button v-if="isPlayable && playing" class="btn btn-link disabled btn-sm mr-2" disabled><i class="fas fa-fw fa-waveform"></i></button>
            <div class="item-title">
                {{ attachment.title ? attachment.title : 'Title Unknown' }}
            </div>
        </div>
        <div class="d-flex">
            <div class="ml-2 mr-4">
                <i :class="icon"></i>
                {{ attachment.category.title }}
            </div>
            <a :href="'/songs/'+song.id+'/attachments/'+attachment.id" class="btn btn-primary btn-sm mr-2"><i class="fa fa-fw fa-download"></i> Download</a>
            <delete-button :action="'/songs/'+song.id+'/attachments/'+attachment.id"></delete-button>
        </div>
    </div>
</template>

<script>
    import DeleteButton from "./DeleteButton";
    export default {
        name: "TrackListItem",
        components: {DeleteButton},
        props: {
            attachment: {
                required: true
            },
            song: {
                required: true
            },
            playing: {
                type: Boolean,
                required: true
            }
        },
        computed: {
            isPlayable() {
                return this.attachment.category.title === 'Learning Tracks'
                    || this.attachment.category.title === 'Full Mix (Demo)'
            },
            icon() {
                if(this.attachment.category.title === 'Learning Tracks'){
                    return 'fa fa-fw fa-file-audio';
                }
                if(this.attachment.category.title === 'Full Mix (Demo)'){
                    return 'fa fa-fw fa-compact-disc';
                }
                if(this.attachment.category.title === 'Sheet Music'){
                    return 'fa fa-fw fa-file-pdf';
                }
                return 'fa fa-fw fa-file-alt';
            }
        }
    }
</script>

<style scoped>

</style>