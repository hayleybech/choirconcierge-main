<template>
    <drop tag="g" @drop="onDrop" mode="cut">

        <drag tag="g" :key="singer.id" :data="singer" @cut="onCut(singer)" :disabled="dragDisabled">
            <svg>
                <circle :cx="coords.centre.x" :cy="coords.centre.y" :r="coords.radius + 15" class="riser-spot-drop-area"></circle>
                <circle :cx="coords.centre.x" :cy="coords.centre.y" :r="coords.radius" :data-singer="singer.name" class="riser-spot" :style="style"></circle>
                <rect v-if="singer.name !== ''" :x="labelPosition.x" :y="labelPosition.y" :width="labelPosition.width" :height="labelPosition.height" class="riser-label"></rect>
                <text v-if="singer.name !== ''" :x="namePosition.x" :y="namePosition.y" text-anchor="middle" class="riser-spot-name">{{ singerInitials }}</text>
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
        colour: {
            type: String,
            default: 'green'
        },
        editDisabled: {
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
                fill: this.fill,
                strokeWidth: '2px',
                stroke: this.colour
            }
        },
        fill() {
            if(this.singer.email === '') {
                return '#ccc';
            }
            return 'url(#img_'+this.singer.id+')';
        },
        imageUrl() {
            if(this.singer.user_avatar_thumb_url === '') {
                return '';
            }

            return this.singer.user_avatar_thumb_url;
        },
        singerInitials() {
            return this.singer.name.split(' ')
                .map( name => name.charAt(0).toUpperCase() )
                .join('');
        },
        namePosition() {
            return {
                x: this.coords.centre.x,
                y: this.coords.centre.y + this.coords.radius + 15
            };
        },
        labelPosition() {
            const height = 15;
            const width  = 35;
            return {
                x: this.namePosition.x - (width / 2),
                y: this.namePosition.y - 12,
                height: height,
                width: width
            }
        },
        dragDisabled() {
            if( this.editDisabled ) {
                return true;
            }

            if( this.singer.id === 0 ) {
                return true;
            }

            return false;
        }
    },
    methods: {
        onDrop(event) {
	        if( this.singer.id > 0 ){
		        this.$parent.$emit('replacedSinger', this.singer);
	        }
            this.$parent.$emit('addedSinger', this.coords, event.data);
        },
        onCut() {
            this.$parent.$emit('removedSinger', this.singer);
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
.riser-spot-name {
    font-size: 12px;
    font-weight: 600;
}
.riser-label {
    fill: #eee;
    rx: 10px;
}
</style>