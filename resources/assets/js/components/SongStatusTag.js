import React from 'react';
import Icon from "./Icon";

const SongStatusTag = ({name, colour, withLabel }) => (
    <>
        <Icon icon="circle" mr className={`text-sm text-${colour}`} />
        {withLabel && <span className="text-sm font-medium text-gray-500 truncate">{name}</span>}
    </>
);

export default SongStatusTag;