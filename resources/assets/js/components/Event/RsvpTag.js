import React from 'react';
import Icon from "../Icon";

const RsvpTag = ({ label, colour, icon, size = 'sm', className }) => (
    <span className={`text-${size} text-${colour}-500 ${className}`}>
        <Icon icon={icon} mr/>
        {label}
    </span>
);

export default RsvpTag;