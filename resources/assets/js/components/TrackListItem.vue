<template>
    <tr>
        <td class="col--play">
            <template v-if="isPlayable">
                <button v-if="isCurrent" class="btn btn-link disabled btn-sm" disabled><i class="fas fa-fw fa-waveform"></i></button>
                <button v-else v-on:click="$emit('play')" class="btn btn-link btn-sm"><i class="fas fa-fw fa-play"></i></button>
            </template>
            <!--
            <template v-else-if="isViewable">
                <button v-if="isCurrent" class="btn btn-link disabled btn-sm" disabled><i class="fas fa-fw fa-book-open"></i></button>
                <button v-else v-on:click="$emit('view')" class="btn btn-link btn-sm"><i class="fas fa-fw fa-book"></i></button>
            </template>
            -->
            <template v-else>
                <button class="btn btn-link disabled btn-sm" disabled><i class="fas fa-fw">&nbsp;</i></button>
            </template>
        </td>
        <td class="col--title">
            {{ attachment.title ? attachment.title : attachment.filepath }}
        </td>
        <td class="col--category">
            <i :class="icon"></i>
            <span class="item-category-title">
                {{ attachment.category.title }}
            </span>
        </td>
        <td class="col--download">
            <a :href="attachment.download_url" class="btn btn-link btn-sm" :download="attachment.filepath"><i class="fa fa-fw fa-download"></i></a>
        </td>
        <td class="col--delete" v-if="canUpdate">
            <delete-button :index="'attachment-'+attachment.id" :action="'/songs/'+song.id+'/attachments/'+attachment.id" class-name="btn-sm" :enable-padding="true"></delete-button>
        </td>
    </tr>
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
            canUpdate: {
                type: Boolean,
                default: false
            }
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
td {
    padding: 1em 0;
}
td:first-child {
    padding-left: 40px;
}
td:last-child {
    padding-right: 40px;
}
.col--play,
.col--download,
.col--delete {
    padding: 0.75em 0;
    width: 1em;
}

@media screen and (max-width: 767px) {
    td:first-child {
        padding-left: 20px;
    }
    td:last-child {
        padding-right: 20px;
    }

    .col--category {
        width: 1em;
    }
    .item-category-title {
        display: none;
    }
}
</style>