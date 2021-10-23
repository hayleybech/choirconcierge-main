import React from 'react';
import Icon from "../Icon";

const RsvpTag = ({ label, colour, icon }) => (
    <span className={`text-sm mr-6 text-${colour}-500`}>
        <Icon icon={icon} mr/>
        {label}
    </span>
);

export default RsvpTag;