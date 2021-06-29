<template>
	<button
		class="pitch btn btn-secondary btn-sm"
		v-on:mousedown="play"
		v-on:mouseup="stop"
		v-on:touchstart="play"
		v-on:touchend="stop"
	>
		<i class="fa fa-play mr-1"></i> <span class="key">{{ note }}</span>
	</button>
</template>

<script>
import { start, Synth } from 'tone';
export default {
	name: 'PitchButton',
	props: {
		note: {
			type: String,
			required: true,
		},
		octave: {
			type: Number,
			default: 4,
		},
	},
	created() {
		this.synth = new Synth().toDestination();
	},
	computed: {
		pitch() {
			return this.note + this.octave.toString();
		},
	},
	methods: {
		play(e) {
			e.stopPropagation();
			e.preventDefault();
			document.addEventListener('mouseup', this.stop);
			document.addEventListener('touchend', this.stop);

			start();
			this.synth.triggerAttack(this.pitch);
		},
		stop(e) {
			e.stopPropagation();
			e.preventDefault();
			document.removeEventListener('mouseup', this.stop);
			document.removeEventListener('touchend', this.stop);

			this.synth.triggerRelease();
		},
	},
};
</script>

<style scoped>
button {
	min-width: 60px;
}
</style>
