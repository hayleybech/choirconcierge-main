import React from 'react';
import Icon from "./Icon";
import DateTag from "./DateTag";
import {DateTime} from "luxon";

const DateUpcomingTag = ({ date, label, format = 'DATE_MED', todayIcon = 'birthday-cake' }) => (
    <>
        {isToday(date)
        ? (
            <div className="flex items-center">
                {todayIcon && <Icon icon={todayIcon} mr />}
                <div className="font-bold">Today</div>
            </div>
        )
        : isTomorrow(date) ? <div className="font-bold">Tomorrow!</div>
        : isThisWeek(date) ? <div className="font-bold">This week!</div>
        : <DateTag date={date} format={format} label={label} />
        }
    </>
);

export default DateUpcomingTag;

function isToday(date) {
    return DateTime.fromISO(date).hasSame(DateTime.now(), 'day');
}
function isTomorrow(date) {
    return DateTime.fromISO(date).hasSame(DateTime.now().plus({ day: 1 }), 'day');
}
function isThisWeek(date) {
    return DateTime.fromISO(date).hasSame(DateTime.now().plus({ day: 1 }), 'week');
}