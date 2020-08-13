<template>
    <button class="pitch btn btn-secondary btn-sm" v-on:mousedown="play" v-on:mouseup="stop">
        <i class="fa fa-play mr-1"></i> <span class="key">{{ note }}</span>
    </button>
</template>

<script>
import { start, Synth } from 'tone';
export default {
    name: "PitchButton",
    props: {
        note: {
            type: String,
            required: true
        },
        octave: {
            type: Number,
            default: 4
        }
    },
    created() {
        this.synth = new Synth().toDestination()
    },
    computed: {
        pitch() {
            return this.note + this.octave.toString();
        }
    },
    mounted() {
        document.addEventListener('mouseup', this.stop);
    },
    methods: {
        play() {
            start();
            this.synth.triggerAttack( this.pitch );
        },
        stop() {
            this.synth.triggerRelease();
        }
    }
}
</script>

<style scoped>
button {
    min-width: 60px;
}
</style>