const TRACK_CAT_SHEET = 1;
const TRACK_CAT_DEMO = 2;
const TRACK_CAT_LEARN = 3;
const TRACK_CAT_OTHER = 4;

class LearningViewer {
	constructor(el, songs) {
		this.songs = songs;
		this.current_song = -1;
		this.current_track = -1;

		// Bind element references
		this.el = el;
		this.el_song_list = this.el.querySelector('.song-list');
		this.el_track_list = this.el.querySelector('.track-list');
		this.el_playing_title = this.el.querySelector('.track-title');

		this.player = new Player(document.querySelector('.audio-panel'));
		//this.viewer = new PDFViewer;
		this.synth = new Tone.Synth().toMaster();
		this.pitch_pipe = new Pitch_Pipe('.pitch');

		this.bindAll();
		this.openSong(0);
	}

	openSong(id) {
		this.current_song = parseInt(id);

		// Set key
		this.pitch_pipe.setKey(this.songs[id].pitch);

		// Update song list
		this.setActiveSongBtn();

		// Load sheet music
		this.openSongSheets();

		// Update track list and load first track.
		this.fillTrackList();
		this.openSongTrack(0);

		return false;
	}

	setActiveSongBtn() {
		let self = this;
		let el_open_song_btns = this.el.querySelectorAll('.open-song');
		Array.from(el_open_song_btns).forEach(function(btn) {
			if (self.current_song === parseInt(btn.dataset.id)) {
				btn.classList.add('active');
				btn.querySelector('i').classList.remove('fa-folder');
				btn.querySelector('i').classList.add('fa-folder-open');
			} else {
				btn.classList.remove('active');
				btn.querySelector('i').classList.remove('fa-folder-open');
				btn.querySelector('i').classList.add('fa-folder');
			}
		});
	}

	setActiveTrackBtn() {
		let self = this;
		let el_open_track_btns = this.el.querySelectorAll('.open-track');

		Array.from(el_open_track_btns).forEach(function(btn) {
			if (self.current_track === parseInt(btn.dataset.id)) {
				btn.classList.add('active');
			} else {
				btn.classList.remove('active');
			}
		});
	}

	openSongSheets() {
		let song = this.songs[this.current_song];
		const CAT_ID_SHEETS = 1;

		let sheets = [];
		for (let i = 0; i < song.attachments.length; i++) {
			if (song.attachments[i].category_id === CAT_ID_SHEETS) {
				sheets.push(song.attachments[i]);
			}
		}
		pageNum = 1; // viewer won't auto reset page num
		openDocument(sheets[0].download_url);
	}

	openSongTrack(track_index) {
		this.current_track = parseInt(track_index);
		this.setActiveTrackBtn();
		this.player.stop();

		let song = this.songs[this.current_song];
		let track = song.attachments[track_index];

		// Confirm this is an audio file
		if (track.category_id !== TRACK_CAT_DEMO && track.category_id !== TRACK_CAT_LEARN) {
			return;
		}

		// Display song title
		this.el_playing_title.textContent = track.title + ' - ' + song.title;

		// Open the track
		this.player.openTrack(track.download_url);
	}

	fillTrackList() {
		let self = this;
		let song = this.songs[this.current_song];

		self.el_track_list.innerHTML = '';
		song.attachments.forEach(function(track, key) {
			// Only list demos or learning tracks.
			if (track.category_id !== TRACK_CAT_DEMO && track.category_id !== TRACK_CAT_LEARN) {
				return;
			}

			let active = key === 0 ? 'active' : '';
			let icon = self.getAttachmentIcon(track);
			// @todo Add a way to represent open/close using the icon/

			// Create link element. Geez, why did I use pure JS and not Vue or jQuery or something?
			let el_item = document.createElement('a');
			el_item.setAttribute('href', '#');
			el_item.setAttribute('data-id', key);
			el_item.setAttribute('class', 'open-track list-group-item list-group-item-action ' + active);
			// Create inner icon element
			let el_icon = document.createElement('i');
			el_icon.setAttribute('class', 'fa ' + icon + ' mr-2');
			el_item.appendChild(el_icon);
			el_item.append(track.title);

			// Append to track list
			self.el_track_list.appendChild(el_item);
		});
	}

	getAttachmentIcon(track) {
		switch (track.category_id) {
			case TRACK_CAT_SHEET:
				return 'fa-file-pdf';
			case TRACK_CAT_DEMO:
				return 'fa-compact-disc';
			case TRACK_CAT_LEARN:
				return 'fa-file-audio';
			default:
				return 'fa-file-alt';
		}
	}

	bindAll() {
		let self = this;

		delegate(this.el_song_list, 'click', 'open-song', function(e) {
			let id = this.dataset.id;
			self.openSong(id);

			e.preventDefault();
		});

		delegate(this.el_track_list, 'click', 'open-track', function(e) {
			let id = this.dataset.id;
			self.openSongTrack(id);

			e.preventDefault();
		});
	}
}

let isSame = document.body.isEqualNode ? 'isEqualNode' : 'isSameNode';
function delegate(wrapperEl, eventName, delegatedElClass, action) {
	wrapperEl.addEventListener(eventName, function(event) {
		let clickedEl = event.target;
		let checkingNode = clickedEl;
		while (checkingNode) {
			if (checkingNode[isSame](wrapperEl)) {
				// checking element itself
				checkingNode = undefined; // STOP loop
			} else {
				if (checkingNode.classList.contains(delegatedElClass)) {
					// found delegated element
					action.call(checkingNode, event); // "this" will be delegated el
					checkingNode = undefined; // STOP loop
				} else {
					// going to parent node
					checkingNode = checkingNode.parentNode;
				}
			}
		}
	});
}
