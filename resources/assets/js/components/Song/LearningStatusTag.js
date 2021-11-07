import React from 'react';
import Icon from "../Icon";

const LearningStatusTag = ({ name, icon, colour }) => (
    <div className={`flex items-center mr-4 font-weight-bold text-${colour}`}>
        <Icon icon={icon} mr />
        {name}
    </div>
);

export default LearningStatusTag;