import React from 'react';

const LearningStatusTag = ({ name, icon, colour }) => (
    <span className={`mr-4 font-weight-bold text-${colour}`}>
        <i className={`fas fa-fw ${icon} mr-2`} />
        {name}
    </span>
);

export default LearningStatusTag;