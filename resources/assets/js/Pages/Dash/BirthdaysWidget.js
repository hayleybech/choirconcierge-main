import React from 'react';
import SectionTitle from "../../components/SectionTitle";
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import Panel from "../../components/Panel";
import DateTag from "../../components/DateTag";
import {DateTime} from "luxon";
import Icon from "../../components/Icon";

const BirthdaysWidget = ({ birthdays, emptyDobs }) => (
    <Panel
        header={<SectionTitle>Birthdays this Month</SectionTitle>}
        footer={<div className="text-gray-500 text-sm">{emptyDobs} active singers have no birthdate listed.</div>}
        noPadding
    >
        {birthdays.length > 0 ? (
            <TableMobile>
                {birthdays.map((user) => (
                    <TableMobileItem url={route('singers.show', user.singer)}>
                        <div className="text-sm font-medium text-purple-800">{user.name}</div>
                        <div className="mr-4">
                            {isToday(user.birthday)
                            ? (
                                <div className="flex items-center">
                                    <Icon icon="birthday-cake" mr />
                                    <div className="font-bold">Today</div>
                                </div>
                            )
                            : isTomorrow(user.birthday) ? <div className="font-bold">Tomorrow!</div>
                            : isThisWeek(user.birthday) ? <div className="font-bold">This week!</div>
                            : <DateTag date={user.birthday} format={isToday(user.birthday) ? 'TIME_24_SIMPLE' : 'DATE_MED'} />
                            }
                        </div>
                    </TableMobileItem>
                ))}
            </TableMobile>
        ) : (
            <p className="px-4 py-4 sm:px-6">No birthdays this month.</p>
        )}
    </Panel>
);

export default BirthdaysWidget;

function isToday(date) {
    return DateTime.fromISO(date).hasSame(DateTime.now(), 'day');
}
function isTomorrow(date) {
    return DateTime.fromISO(date).hasSame(DateTime.now().plus({ day: 1 }), 'day');
}
function isThisWeek(date) {
    return DateTime.fromISO(date).hasSame(DateTime.now().plus({ day: 1 }), 'week');
}