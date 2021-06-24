class Player {
	constructor(el) {
		this.el = el;
		this.el_seek = el.querySelector('.audio-seek');
		this.el_play_btn = el.querySelector('.play');
		this.el_pause_btn = el.querySelector('.pause');
		this.el_position = el.querySelector('.time-position');
		this.el_length = el.querySelector('.time-length');

		this.howl = null;
		this.seek_timer = null;

		this.bindAll();
		this.updatePlayPauseBtns();
	}

	play() {
		if (this.howl && !this.howl.playing()) {
			this.howl.play();
		}
	}
	pause() {
		if (this.howl && this.howl.playing()) {
			this.howl.pause();
		}
	}

	stop() {
		if (this.howl && this.howl.playing()) {
			this.howl.stop();
		}
	}

	openTrack(src) {
		let self = this;

		this.howl = new Howl({
			src: [src],
			onplay: function() {
				self.displayTimeLength();

				self.updatePlayPauseBtns();
				self.startSeekBar();
			},
			onpause: function() {
				self.updatePlayPauseBtns();
				self.stopSeekBar();
			},
			onstop: function() {
				self.updatePlayPauseBtns();
				self.stopSeekBar();
			},
			onend: function() {
				self.updatePlayPauseBtns();
				self.stopSeekBar();
			},
			onload: function() {
				self.displayTimeLength();
				self.step();
			},
		});
	}

	startSeekBar() {
		this.seek_timer = setInterval(this.step.bind(this), this.STEP_FREQ());
	}
	stopSeekBar() {
		clearInterval(this.seek_timer);
	}
	seekTo(percent) {
		if (this.howl.playing()) {
			this.howl.seek(this.howl.duration() * percent);
		}
	}

	step() {
		let self = this;
		let seek_pos = this.howl.seek() || 0;

		// Update timer
		this.el_position.innerHTML = this.formatTime(Math.round(seek_pos));

		// Determine our current seek position.
		this.el_seek.value = seek_pos / this.howl.duration() || 0;
	}
	STEP_FREQ() {
		return 250;
	}

	displayTimeLength() {
		this.el_length.innerHTML = this.formatTime(Math.round(this.howl.duration()));
	}

	updatePlayPauseBtns() {
		if (this.howl && this.howl.playing()) {
			this.el_play_btn.style.display = 'none';
			this.el_pause_btn.style.display = 'block';
		} else {
			this.el_play_btn.style.display = 'block';
			this.el_pause_btn.style.display = 'none';
		}
	}

	bindAll() {
		let self = this;

		// Bind seek bar events
		this.el_seek.addEventListener('mousedown', function(e) {
			if (self.howl.playing()) {
				self.stopSeekBar();
			}
		});
		this.el_seek.addEventListener('change', function(e) {
			self.seekTo(e.target.value);
		});
		this.el_seek.addEventListener('mouseup', function(e) {
			if (self.howl.playing()) {
				self.startSeekBar();
			}
		});

		// Bind play/pause
		this.el_play_btn.addEventListener('click', function() {
			self.play();
		});
		this.el_pause_btn.addEventListener('click', function() {
			self.pause();
		});
	}
}
