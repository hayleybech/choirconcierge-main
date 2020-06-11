<template>
    <drop tag="g" @drop="onDrop" mode="cut">

        <drag tag="g" :key="singer.id" :data="singer" @cut="onCut(singer)" :disabled="disabled">
            <svg>
                <circle :cx="coords.centre.x" :cy="coords.centre.y" :r="coords.radius + 15" class="riser-spot-drop-area"></circle>
                <circle :cx="coords.centre.x" :cy="coords.centre.y" :r="coords.radius" :data-singer="singer.name" class="riser-spot" :style="style"></circle>
            </svg>
            <template v-slot:drag-image="{ data }">
                <svg>
                    <circle :cx="coords.radius" :cy="coords.radius" :r="coords.radius" :data-singer="singer.name" class="riser-spot" :style="style"></circle>
                </svg>
            </template>
        </drag>

        <defs>
            <pattern :id="'img_'+singer.id" patternUnits="objectBoundingBox" patternContentUnits="objectBoundingBox" width="1" height="1">
                <image :xlink:href="imageUrl" x="0" y="0" width="1" height="1" />
            </pattern>
        </defs>
    </drop>
</template>

<script>
import {Drop, Drag} from 'vue-easy-dnd';
export default {
    name: "RiserSpot",
    props: {
        coords: Object,
        singer: {
            type: Object,
            default: {
                id: 0,
                name: '',
                email: '',
                part: 0
            }
        },
        disabled: {
            type: Boolean,
            default: false
        }
    },
    components: {
        Drop,
        Drag
    },
    computed: {
        style() {
            return {
                backgroundImage: 'url('+this.imageUrl+')',
                fill: this.fill
            }
        },
        fill() {
            if(this.singer.email === '') {
                return '#ccc';
            }
            return 'url(#img_'+this.singer.id+')';
        },
        imageUrl() {
            if(this.singer.email === '') {
                return '';
            }

            return 'https://api.adorable.io/avatars/50/'+this.singer.email+'.png';
        }
    },
    methods: {
        onDrop(event) {
            //this.singer = event.data;
            this.$parent.$emit('addedSinger', this.coords, event.data);
        },
        onCut() {
            /*this.singer = {
                id: 0,
                name: '',
                email: '',
                part: ''
            };*/
            this.$parent.$emit('removedSinger', this.coords);
        }
    }
}
</script>

<style scoped>
.riser-spot-drop-area {
    fill: transparent;
    stroke: transparent;
    stroke-width: 1px;
}
</style>