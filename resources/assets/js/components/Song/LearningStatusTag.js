import React from 'react';
import Icon from "../Icon";

const LearningStatusTag = ({ status }) => (
    <div className={`flex items-center mr-4 font-bold ${status.textColour}`}>
        <Icon icon={status.icon} mr />
        {status.title}
    </div>
);

export default LearningStatusTag;