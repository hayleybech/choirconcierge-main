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
                    <TableMobileItem url={route('singers.show', singer)}>
                        <div className="text-sm font-medium text-purple-800">{singer.user.name}</div>
                        <div className="mr-4">
                            <DateUpcomingTag date={DateTime.fromISO(singer.joined_at).set({ year: DateTime.now().year })} />
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