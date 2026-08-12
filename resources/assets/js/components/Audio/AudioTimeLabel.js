import React, { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';

const formatTime = seconds => {
	if (isNaN(seconds) || seconds === null) return '0:00';
	const floored = Math.floor(seconds);
	let from = 14;
	let length = 5;
	// Display hours only if necessary.
	if (floored >= 3600) {
		from = 11;
		length = 8;
	}

	return new Date(floored * 1000).toISOString().substr(from, length);
};

export const AudioTimeLabel = ({ show = 'both', position }) => {
	const { duration } = useContext(PlayerContext);

	if (duration === Infinity || duration === 0) return null;
	const elapsed = typeof position === 'number' ? position : 0;

	return (
		<div className="text-gray-500 text-xs">
			{
				{
					elapsed: formatTime(elapsed),
					length: formatTime(duration),
					both: `${formatTime(elapsed)}/${formatTime(duration)}`,
				}[show]
			}
		</div>
	);
};
