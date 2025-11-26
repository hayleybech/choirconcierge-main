import React from 'react';
import Icon from "./Icon";
import {DateTime} from "luxon";

const DateTag = ({ date, label, icon = 'calendar-day', format = 'DATE_MED', mr = true, className = '' }) => (
    <div className={className}>
        <Icon icon={icon} type="regular" mr={mr} className="text-gray-400" />
        {label} {DateTime.fromJSDate(new Date(date)).toLocaleString(DateTime[format])}
    </div>
);

export default DateTag;