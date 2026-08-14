import React, { useContext } from 'react';
import Button from '../inputs/Button';
import { Popover } from '@headlessui/react';
import { PlayerContext } from '../../contexts/player-context';

const SEMITONES = [-3, -2, -1, 0, 1, 2, 3];

const label = st => {
	if (st === 0) return '0st';
	return (st > 0 ? '+' : '') + st + 'st';
};

export const AudioPitchButton = () => {
	const { pitch, setPitch } = useContext(PlayerContext);

	return (
		<Popover className="relative">
			<Popover.Button as={Button} variant="clear" size="xs" className="tabular-nums w-12 text-xs">
				{label(pitch)}
			</Popover.Button>
			<Popover.Panel className="absolute bottom-full right-0 bg-white rounded border border-gray-200 shadow-lg py-1 min-w-max">
				{({ close }) =>
					[...SEMITONES].reverse().map(st => (
						<button
							key={st}
							onClick={() => {
								setPitch(st);
								close();
							}}
							className={`block w-full text-left px-4 py-1.5 text-sm hover:bg-gray-100 ${
								st === pitch ? 'font-semibold text-purple-700' : 'text-gray-700'
							}`}
						>
							{label(st)}
						</button>
					))
				}
			</Popover.Panel>
		</Popover>
	);
};
