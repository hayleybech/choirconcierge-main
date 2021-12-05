import React from 'react';
import Icon from "../Icon";

const AttendanceTag = ({ label, colour, icon }) => (
    <span className={`text-sm mr-6 text-${colour}-500`}>
        <Icon icon={icon} mr/>
        {label}
    </span>
);

export default AttendanceTag;