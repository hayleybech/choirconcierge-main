import React, { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';

const formatTime = seconds => {
	if (isNaN(seconds) || seconds === null) return '0:00';
	const s = Math.floor(seconds);
	const h = Math.floor(s / 3600);
	const m = Math.floor((s % 3600) / 60);
	const sec = s % 60;
	const mm = String(m).padStart(2, '0');
	const ss = String(sec).padStart(2, '0');
	return h > 0 ? `${h}:${mm}:${ss}` : `${m}:${ss}`;
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
