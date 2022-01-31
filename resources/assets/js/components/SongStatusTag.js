import React from 'react';
import Icon from "./Icon";

const SongStatusTag = ({ status, withLabel }) => (
    <>
        <Icon icon={status.icon} mr className={`text-sm ${status.textColour}`} />
        {withLabel && <span className="text-sm font-medium text-gray-500 truncate">{status.title}</span>}
    </>
);

export default SongStatusTag;