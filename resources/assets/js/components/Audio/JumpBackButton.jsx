import Icon from '../Icon';
import ButtonV2 from '../inputs/ButtonV2';
import React, { useContext } from 'react';
import { PlayerContext } from '../../contexts/player-context';
 
export const JumpBackButton = () => {
	const { jumpBack } = useContext(PlayerContext);

	return (
		<ButtonV2 intent="secondary" weight="clear" size="sm" className="relative" onClick={jumpBack}>
			<Icon icon="undo" type="regular" size="text-[20px]" />
			<div className="absolute mt-0.5 font-black inset-0 text-[8px] flex items-center justify-center text-gray-700">
				10
			</div>
		</ButtonV2>
	);
}