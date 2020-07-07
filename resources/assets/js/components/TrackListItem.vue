<template>
    <div class="list-group-item d-flex justify-content-between">
        <div class="d-flex">
            <template v-if="isPlayable">
                <button v-if="isCurrent" class="btn btn-link disabled btn-sm mr-2" disabled><i class="fas fa-fw fa-waveform"></i></button>
                <button v-else v-on:click="$emit('play')" class="btn btn-link btn-sm mr-2"><i class="fas fa-fw fa-play"></i></button>
            </template>
            <!--
            <template v-else-if="isViewable">
                <button v-if="isCurrent" class="btn btn-link disabled btn-sm mr-2" disabled><i class="fas fa-fw fa-book-open"></i></button>
                <button v-else v-on:click="$emit('view')" class="btn btn-link btn-sm mr-2"><i class="fas fa-fw fa-book"></i></button>
            </template>
            -->
            <template v-else>
                <button class="btn btn-link disabled btn-sm mr-2" disabled><i class="fas fa-fw">&nbsp;</i></button>
            </template>
            <div class="item-title">
                {{ attachment.title ? attachment.title : attachment.filepath }}
            </div>
        </div>
        <div class="d-flex">
            <div class="ml-2 mr-4">
                <i :class="icon"></i>
                <span class="item-category-title">
                    {{ attachment.category.title }}
                </span>
            </div>
            <a :href="'/songs/'+song.id+'/attachments/'+attachment.id" class="btn btn-link btn-sm mr-2"><i class="fa fa-fw fa-download"></i></a>
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
            isCurrent: {
                type: Boolean,
                required: true
            },
        },
        computed: {
            isPlayable() {
                return this.attachment.category.title === 'Learning Tracks'
                    || this.attachment.category.title === 'Full Mix (Demo)'
            },
            isViewable() {
                return this.attachment.category.title === 'Sheet Music';
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
@media screen and (max-width: 767px) {
    .item-category-title {
        display: none;
    }
}
</style>