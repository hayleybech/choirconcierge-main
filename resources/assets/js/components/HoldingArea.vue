<template>
    <div :class="'card border-'+theme">
        <h5 :class="'card-header bg-transparent border-'+theme">{{ title }}</h5>

        <drop-list class="card-body" :items="singers" @insert="onInsert" @reorder="$event.apply(singers)" :accepts-data="accept" mode="cut">
            <template v-slot:item="{ item: singer }">
                <drag :key="singer.id" :data="singer" @cut="onCut(singer)">
                    <riser-face :singer="singer"></riser-face>
                </drag>
            </template>
            <template v-slot:feedback="{ data: singer }">
                <div :key="singer.id">
                    <riser-face :singer="singer"></riser-face>
                </div>
            </template>
        </drop-list>
    </div>
</template>

<script>
import {DropList, Drag} from "vue-easy-dnd";

export default {
    name: "HoldingArea",
    components: {
        DropList,
        Drag
    },
    props: {
        title: String,
        singers: {
            type: Array,
            default: () => []
        },
        part: String,
        theme: String
    },
    data() {
        return {

        }
    },
    methods: {
        onInsert(event) {
            this.singers.push(event.data);
        },
        onCut(value) {
            let index = this.singers.indexOf(value);
            this.singers.splice(index, 1);
        },
        accept(data) {
            if(this.part === 'baritone' || this.part === 'bass') {
                return (data.part === 'baritone' || data.part === 'bass');
            }
            return (data.part === 'lead' || data.part === 'tenor');
        }
    }
}
</script>

<style scoped>
    .card-body {
        display: flex;
        flex-flow: row wrap;
    }
    .card-body > * {
        margin-right: 10px;
    }
</style>