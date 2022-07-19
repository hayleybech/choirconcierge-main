import React from 'react';
import SectionTitle from "../../components/SectionTitle";
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import Panel from "../../components/Panel";
import DateUpcomingTag from "../../components/DateUpcomingTag";
import {DateTime} from "luxon";

const MemberversariesWidget = ({ memberversaries }) => (
    <Panel header={<SectionTitle>Memberversaries this Month</SectionTitle>} noPadding>
        {memberversaries.length > 0 ? (
            <TableMobile>
                {memberversaries.map((singer) => (
                    <TableMobileItem url={route('singers.show', singer)} key={singer.id}>
                        <div className="text-sm font-medium text-purple-800">{singer.user.name}</div>
                        <div className="flex items-center mr-4">
                            <div className="text-sm text-gray-700 mr-2">
                                {DateTime.fromISO(singer.memberversary).diff(DateTime.fromISO(singer.joined_at), 'years').years} Years
                            </div>
                            <DateUpcomingTag date={DateTime.fromISO(singer.memberversary)} />
                        </div>
                    </TableMobileItem>
                ))}
            </TableMobile>
        ) : (
            <p className="px-4 py-4 sm:px-6">No memberversaries this month.</p>
        )}
    </Panel>
);

export default MemberversariesWidget;