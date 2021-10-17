import React from 'react';
import Icon from "../Icon";

const LearningStatusTag = ({ name, icon, colour }) => (
    <span className={`mr-4 font-weight-bold text-${colour}`}>
        <Icon icon={icon} mr />
        {name}
    </span>
);

export default LearningStatusTag;