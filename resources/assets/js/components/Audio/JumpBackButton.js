import Icon from '../Icon';
import Button from '../inputs/Button';
import React, { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';

export const JumpBackButton = () => {
	const { jumpBack } = useContext(PlayerContext);

	return (
		<Button variant="clear" size="xs" className="relative" onClick={jumpBack}>
			<Icon icon="undo" type="regular" size="text-[20px]" />
			<div className="absolute mt-0.5 font-black inset-0 text-[8px] flex group-hover:text-purple-500 items-center justify-center text-gray-700">
				10
			</div>
		</Button>
	);
}