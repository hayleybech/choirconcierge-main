import React, { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';
import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '../../../../../tailwind.config';
import Label from '../inputs/Label';

const PURPLE_500 = resolveConfig(tailwindConfig).theme.colors.purple[500] ?? '#7c3aed';

const MIN_RATE = 0.5;
const MAX_RATE = 1.5;
const STEP = 0.1;
const STEP_DISPLAY = 0.5;

const RATES = Array.from({ length: Math.floor((MAX_RATE - MIN_RATE) / STEP_DISPLAY) + 1 }, (_, i) => {
	const rawValue = MIN_RATE + i * STEP_DISPLAY;
	return parseFloat(rawValue.toFixed(2)); // Cleans up floating-point math issues
});

export const TempoControl = () => {
	const { rate, setRate } = useContext(PlayerContext);

	return (
		<>
			<Label>Tempo: {rate}x</Label>
			<input
				type="range"
				min={MIN_RATE}
				max={MAX_RATE}
				step={STEP}
				value={rate}
				onChange={e => setRate(Number(e.target.value))}
				list="tempo-ticks"
				className="w-full"
				style={{ accentColor: PURPLE_500 }}
			/>
			<datalist id="tempo-ticks" className="flex flex-row text-sm text-gray-600 justify-between">
				{RATES.map(r => (
					<option key={r} value={r} label={r === 1 ? '1.0x' : `${r}x`}></option>
				))}
			</datalist>
		</>
	);
};
