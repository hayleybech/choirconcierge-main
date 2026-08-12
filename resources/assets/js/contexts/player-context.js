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
	rate: 1,
	setRate: rate => {},
	showFullscreen: false,
	setShowFullscreen: value => {},
	getPosition: () => 0,
});
