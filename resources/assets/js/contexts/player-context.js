import React from 'react';

export const PlayerContext = React.createContext({
	songTitle: null,
	songId: 0,
	fileName: null,
	src: null,
	playing: false,
	loading: false,
	duration: 0,
	volume: 1,
	play: attachment => {},
	pause: () => {},
	togglePlayPause: () => {},
	stop: () => {},
	seek: pos => {},
	setVolume: vol => {},
	showFullscreen: false,
	setShowFullscreen: value => {},
});
