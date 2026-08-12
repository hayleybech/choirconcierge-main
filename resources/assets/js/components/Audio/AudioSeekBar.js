import React, { useCallback, useContext, useRef } from 'react';
import { PlayerContext } from '../../contexts/player-context';

export const AudioSeekBar = ({ position, className }) => {
	const { duration, seek } = useContext(PlayerContext);
	const percentComplete = duration > 0 ? (position / duration) * 100 : 0;
	const barWidth = `${percentComplete}%`;

	const seekBarElem = useRef(null);

	const goTo = useCallback(
		event => {
			const { pageX: eventOffsetX } = event;

			if (seekBarElem.current && duration > 0) {
				const elementOffsetX = seekBarElem.current.getBoundingClientRect().left;
				const elementWidth = seekBarElem.current.clientWidth;
				const percent = (eventOffsetX - elementOffsetX) / elementWidth;
				seek(percent * duration);
			}
		},
		[duration, seek]
	);

	return (
		<div
			className={`bg-gray-800 cursor-pointer overflow-hidden grow sm:w-64 h-4 rounded ${className}`}
			ref={seekBarElem}
			onClick={goTo}
		>
			<div style={{ width: barWidth }} className="bg-purple-500 h-full" />
		</div>
	);
};
