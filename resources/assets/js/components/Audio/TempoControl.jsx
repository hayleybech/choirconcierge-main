import React, { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';
import Label from '../inputs/Label';
import ButtonV2 from '../inputs/ButtonV2';
import Icon from '../Icon';
 
import clamp from 'lodash/clamp';

const PURPLE_500 = 'var(--color-purple-500)';

const MIN_RATE = 50;
const MAX_RATE = 150;
const STEP = 5;
const STEP_DISPLAY = 50;

const TICKS = Array.from({ length: Math.floor((MAX_RATE - MIN_RATE) / STEP_DISPLAY) + 1 }, (_, i) => {
	return MIN_RATE + i * STEP_DISPLAY;
});

export const TempoControl = () => {
	const { rate, setRate } = useContext(PlayerContext);

	return (
		<>
			<Label>Tempo: {rate}%</Label>
			<div className="flex gap-2">
				<ButtonV2 intent="secondary" weight="outline" size="sm" onClick={() => setRate(clamp(rate - STEP, MIN_RATE, MAX_RATE))}>
					<Icon icon="minus" />
				</ButtonV2>
				<div className="flex-grow">
					<input
						type="range"
						min={MIN_RATE}
						max={MAX_RATE}
						step={STEP}
						value={rate}
						onChange={e => setRate(parseFloat(Number(e.target.value).toFixed(2)))}
						list="tempo-ticks"
						className="w-full"
						style={{ accentColor: PURPLE_500 }}
					/>
					<datalist id="tempo-ticks" className="flex flex-row text-sm text-gray-600 justify-between">
						{TICKS.map(r => (
							<option key={r} value={r} label={`${r}%`}></option>
						))}
					</datalist>
				</div>
				<ButtonV2 intent="secondary" weight="outline" size="sm" onClick={() => setRate(clamp(rate + STEP, MIN_RATE, MAX_RATE))}>
					<Icon icon="plus" />
				</ButtonV2>
			</div>
		</>
	);
};
