import React from 'react';
import Icon from "./Icon";
import SingerStatus from "../SingerStatus";

const SingerStatusTag = ({ status, withLabel }) => {
    const singerStatus = new SingerStatus(typeof status === 'string' ? status : status?.slug);

    return (
        <span>
            <Icon icon={singerStatus.icon} mr={withLabel} className={`text-sm ${singerStatus.textColour}`} />
            {withLabel && <span className="text-sm font-medium text-gray-500 truncate">{singerStatus.title}</span>}
        </span>
    );
};

export default SingerStatusTag;