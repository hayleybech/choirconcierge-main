<template>
    <div :class="'card border-'+theme">
        <h5 :class="'card-header bg-transparent border-'+theme">{{ title }}</h5>

        <drop-list class="list-group-flush" :items="singers" @insert="onInsert" @reorder="$event.apply(singers)" :accepts-data="accept" mode="cut">
            <template v-slot:item="{ item: singer }">
                <drag class="list-group-item" :key="singer.id" :data="singer" @cut="onCut(singer)">
                    <riser-face :singer="singer"></riser-face>
                    
                    <!--<template v-slot:drag-image="{ data }">
                        <riser-face :singer="singer"></riser-face>
                    </template>-->
                </drag>
            </template>
            <template v-slot:feedback="{ data: singer }">
                <div class="list-group-item" :key="singer.id">
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
        part: Number,
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
            //return this.part === data.voice_part_id;
            return true;
        }
    }
}
</script>

<style scoped>
.list-group-flush {
    min-height: 45px;
}
</style>