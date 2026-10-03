import React from 'react';
import Icon from "../Icon";

const AttendanceTag = ({ label, colour, icon, type = icon === 'circle' ? 'regular' : 'solid', size = 'sm', className = '' }) => (
    <span className={`text-${size} text-${colour}-500 ${className}`}>
        <Icon icon={icon} mr={!!label} type={type} />
        {label}
    </span>
);

export default AttendanceTag;