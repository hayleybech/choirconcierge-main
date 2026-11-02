import React from 'react';
import Icon from "./Icon";
 
const SingerStatusTag = ({ status, withLabel, compact = false }) => {
    return (
        <span className={compact ? 'space-x-0.5' : 'space-x-1.5'}>
            <Icon icon={status.icon} className={`${compact ? 'text-[10px]' : 'text-sm'} ${status.textColour}`} />
            {withLabel && <span className={`${compact ? 'text-xs' : 'text-sm'} font-medium text-gray-500 truncate`}>{status.title}</span>}
        </span>
    );
};

export default SingerStatusTag;