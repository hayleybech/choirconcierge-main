import React, { useContext } from 'react';
import Button from '../inputs/Button';
import { Popover } from '@headlessui/react';
import { PlayerContext } from '../../contexts/player-context';

const RATES = [0.7, 0.8, 0.9, 1, 1.1, 1.2, 1.3];

export const AudioTempoButton = () => {
	const { rate, setRate } = useContext(PlayerContext);

	return (
		<Popover className="relative">
			<Popover.Button as={Button} variant="clear" size="xs" className="tabular-nums w-10 text-xs">
				{rate === 1 ? '1.0x' : `${rate}x`}
			</Popover.Button>
			<Popover.Panel className="absolute bottom-full right-0 bg-white rounded border border-gray-200 shadow-lg py-1 min-w-max">
				{({ close }) =>
					RATES.map(r => (
						<button
							key={r}
							onClick={() => {
								setRate(r);
								close();
							}}
							className={`block w-full text-left px-4 py-1.5 text-sm hover:bg-gray-100 ${
								r === rate ? 'font-semibold text-purple-700' : 'text-gray-700'
							}`}
						>
							{r === 1 ? '1.0x' : `${r}x`}
						</button>
					))
				}
			</Popover.Panel>
		</Popover>
	);
};
