import React from 'react';
import Icon from './Icon';

const SongStatusTag = ({ status, withLabel, compact = false }) => (
	<span className={compact ? 'space-x-0.5' : ''}>
		<Icon
			icon={status.icon}
			mr={!compact}
			className={`${compact ? 'text-[10px]' : 'text-sm'} ${status.textColour}`}
		/>
		{withLabel && (
			<span className={`${compact ? 'text-xs' : 'text-sm'} font-medium text-gray-500 truncate`}>
				{status.title}
			</span>
		)}
	</span>
);

export default SongStatusTag;
