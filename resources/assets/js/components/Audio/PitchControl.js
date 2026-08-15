import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '../../../../../tailwind.config';
import Label from '../inputs/Label';
import React, { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';
import Icon from '../Icon';
import Button from '../inputs/Button';

import clamp from 'lodash/clamp';

const PURPLE_500 = resolveConfig(tailwindConfig).theme.colors.purple[500] ?? '#7c3aed';

const MIN_PITCH = -12;
const MAX_PITCH = 12;
const STEP = 1;
const STEP_DISPLAY = 6;

const TICKS = Array.from({ length: Math.floor((MAX_PITCH - MIN_PITCH) / STEP_DISPLAY) + 1 }, (_, i) => {
	const rawValue = MIN_PITCH + i * STEP_DISPLAY;
	return parseFloat(rawValue.toFixed(2)); // Cleans up floating-point math issues
});

const getLabelForPitch = st => {
	if (st === 0) return 'Original';
	return (st > 0 ? '+' : '') + st + 'st';
};

export const PitchControl = () => {
	const { pitch, setPitch } = useContext(PlayerContext);

	return (
		<>
			<Label>Pitch: {getLabelForPitch(pitch)}</Label>
			<div className="flex gap-2">
				<Button
					variant="secondary"
					size="xs"
					onClick={() => setPitch(clamp(pitch - STEP, MIN_PITCH, MAX_PITCH))}
				>
					<Icon icon="minus" />
				</Button>
				<div className="flex-grow">
					<input
						type="range"
						min={MIN_PITCH}
						max={MAX_PITCH}
						step={STEP}
						value={pitch}
						onChange={e => setPitch(Number(e.target.value))}
						list="pitch-ticks"
						className="w-full"
						style={{ accentColor: PURPLE_500 }}
					/>
					<datalist id="pitch-ticks" className="flex flex-row text-sm text-gray-600 justify-between">
						{TICKS.map(p => (
							<option key={p} value={p} label={p > 0 ? `+${p}` : p}></option>
						))}
					</datalist>
				</div>
				<Button
					variant="secondary"
					size="xs"
					onClick={() => setPitch(clamp(pitch + STEP, MIN_PITCH, MAX_PITCH))}
				>
					<Icon icon="plus" />
				</Button>
			</div>
		</>
	);
};
