import React from 'react';

const SingerCategoryTag = ({name, colour, withLabel }) => (
    <>
        <i className={"fas fa-fw fa-circle mr-1.5 text-sm text-"+colour} />
        {withLabel && <span className="text-sm font-medium text-gray-500 truncate">{name}</span>}
    </>
);

export default SingerCategoryTag;