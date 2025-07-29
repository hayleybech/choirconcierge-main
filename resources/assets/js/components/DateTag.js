import React from 'react';
import Icon from "./Icon";
import {DateTime} from "luxon";

const DateTag = ({ date, label, format = 'DATE_MED', className = '' }) => (
    <div className={className}>
        <Icon icon="calendar-day" type="regular" mr className="text-gray-400" />
        {label} {DateTime.fromJSDate(new Date(date)).toLocaleString(DateTime[format])}
    </div>
);

export default DateTag;