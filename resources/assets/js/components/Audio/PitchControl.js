import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '../../../../../tailwind.config';
import Label from '../inputs/Label';
import { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';

const PURPLE_500 = resolveConfig(tailwindConfig).theme.colors.purple[500] ?? '#7c3aed';

const getLabelForPitch = st => {
	if (st === 0) return 'Original';
	return (st > 0 ? '+' : '') + st + 'st';
};

export const PitchControl = () => {
	const { pitch, setPitch } = useContext(PlayerContext);

	return (
		<>
			<Label>Pitch: {getLabelForPitch(pitch)}</Label>

			<input
				type="range"
				min={-12}
				max={12}
				step={1}
				value={pitch}
				onChange={e => setPitch(Number(e.target.value))}
				list="pitch-ticks"
				className="w-full"
				style={{ accentColor: PURPLE_500 }}
			/>
			<datalist id="pitch-ticks" className="flex flex-row text-sm text-gray-600 justify-between">
				<option value="-12" label="-12"></option>
				<option value="-6" label="-6"></option>
				<option value="0" label="0"></option>
				<option value="6" label="+6"></option>
				<option value="12" label="+12"></option>
			</datalist>
		</>
	);
};
