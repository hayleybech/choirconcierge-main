import resolveConfig from 'tailwindcss/resolveConfig';
import tailwindConfig from '../../../../../tailwind.config';
import React, { useCallback, useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';
import Label from '../inputs/Label';

const PURPLE_500 = resolveConfig(tailwindConfig).theme.colors.purple[500] ?? '#7c3aed';
export const PanControl = () => {
	const { pan, setPan } = useContext(PlayerContext);

	const handleChange = useCallback(e => setPan(Number(e.target.value)), [
		setPan,
	]);

	return (
		<>
			<Label>Pan</Label>
			<div className="flex items-center space-x-2">
				<Label>L</Label>
				<input
					type="range"
					min={-1}
					max={1}
					step={0.1}
					value={pan}
					onChange={handleChange}
					className="flex-grow"
					style={{ accentColor: PURPLE_500 }}
				/>
				<Label>
					R
				</Label>
			</div>
		</>
	);
};
