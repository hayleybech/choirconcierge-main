<template>
	<div class="audio-controls">
		<div class="track-title">{{ title }}</div>
		<input
			type="range"
			class="audio-seek custom-range"
			:value="seek_value"
			v-on:mousedown="stopSeekBar"
			v-on:mouseup="startSeekBar"
			v-on:change="seekTo"
			min="0"
			max="1"
			step="0.001"
		/>
		<div class="play-controls">
			<button v-if="!playing()" v-on:click="play()" class="play btn btn-primary mr-1">
				<i class="fa fa-play"></i>
			</button>
			<button v-if="playing()" v-on:click="pause()" class="pause btn btn-primary mr-1">
				<i class="fa fa-pause"></i>
			</button>

			<div class="time">
				<span class="time-position">{{ track_time }}</span> /
				<span class="time-length">{{ track_length }}</span>
			</div>
		</div>
	</div>
</template>

<script>
import { Howl, Howler } from 'howler';

export default {
	name: 'track-player',
	props: {
		title: {
			type: String,
			required: true,
		},
		src: {
			type: String,
			required: true,
		},
	},
	created() {
		this.STEP_FREQ = 250;
	},
	data() {
		return {
			howl: null,
			track_length: this.formatTime(0),
			track_time: this.formatTime(0),
			seek_value: 0,
		};
	},
	watch: {
		src() {
			this.stop();
			this.openTrack(this.src);
			this.play();
		},
	},
	methods: {
		play() {
			if (this.playing()) {
				return;
			}

			if (!this.howl) {
				this.openTrack(this.src);
			}
			this.howl.play();
		},
		pause() {
			if (!this.playing()) {
				return;
			}
			this.howl.pause();
		},
		stop() {
			if (!this.playing()) {
				return;
			}
			this.howl.stop();
		},
		playing() {
			return this.howl && this.howl.playing();
		},

		updateTrackLength() {
			this.track_length = this.formatTime(Math.round(this.howl.duration()));
		},

		startSeekBar() {
			if (!this.playing()) {
				return;
			}
			this.seek_timer = setInterval(this.step.bind(this), this.STEP_FREQ);
		},
		stopSeekBar() {
			if (!this.playing()) {
				return;
			}
			clearInterval(this.seek_timer);
		},
		seekTo(e) {
			if (!this.playing()) {
				return;
			}
			this.howl.seek(this.howl.duration() * e.target.value);
		},
		step() {
			let seek_pos = this.howl.seek() || 0;

			// Update timer
			this.track_time = this.formatTime(Math.round(seek_pos));

			// Determine our current seek position.
			this.seek_value = seek_pos / this.howl.duration() || 0;
		},

		/**
		 * Open Track
		 * Creates the Howl instance
		 */
		openTrack(src) {
			let self = this;

			this.howl = new Howl({
				src: [src],
				onplay: function() {
					self.updateTrackLength();

					self.startSeekBar();
				},
				onpause: function() {
					self.stopSeekBar();
				},
				onstop: function() {
					self.stopSeekBar();
				},
				onend: function() {
					self.stopSeekBar();
				},
				onload: function() {
					self.updateTrackLength();
					self.step();
				},
			});
		},

		/**
		 * Format the time from seconds to M:SS.
		 * @param  {Number} secs Seconds to format.
		 * @return {String}      Formatted time.
		 */
		formatTime(secs) {
			let minutes = Math.floor(secs / 60) || 0;
			let seconds = secs - minutes * 60 || 0;

			return minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
		},
	},
};
</script>

<style scoped></style>
