import React from 'react';
import SectionTitle from "../../components/SectionTitle";
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import Panel from "../../components/Panel";
import DateUpcomingTag from "../../components/DateUpcomingTag";

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
                            <DateUpcomingTag date={user.birthday} />
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