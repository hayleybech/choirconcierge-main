import React from 'react';
import Icon from "../Icon";

const AttendanceTag = ({ label, colour, icon }) => (
    <span className={`text-sm text-${colour}-500`}>
        <Icon icon={icon} mr={!!label} />
        {label}
    </span>
);

export default AttendanceTag;