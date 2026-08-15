import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '../../../../../tailwind.config';
import React, { useCallback, useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';
import Label from '../inputs/Label';
import Icon from '../Icon';

const PURPLE_500 = resolveConfig(tailwindConfig).theme.colors.purple[500] ?? '#7c3aed';
export const VolumeControl = () => {
	const { volume, setVolume } = useContext(PlayerContext);

	const handleChange = useCallback(e => setVolume(parseFloat((Number(e.target.value) / 100).toFixed(2))), [
		setVolume,
	]);

	return (
		<>
			<Label>Volume</Label>
			<div className="flex items-center space-x-2">
				<Icon icon="volume-down" className="text-gray-700" />
				<input
					type="range"
					min={0}
					max={100}
					value={Math.round(volume * 100)}
					onChange={handleChange}
					style={{ accentColor: PURPLE_500 }}
				/>
				<Icon icon="volume-up" className="text-gray-700" />
			</div>
		</>
	);
};
