import React from 'react';
import Table, {TableCell} from "../../../components/Table";
import collect from "collect.js";
import DateTag from "../../../components/DateTag";
import {Link} from "@inertiajs/react";
import useRoute from "../../../hooks/useRoute";
import Icon from '../../../components/Icon';
import MailStatusTag from '../../../components/MailStatusTag';
import Pagination from '../../../components/Pagination';

const MailLogTableDesktop = ({ logs }) => {
    const { route } = useRoute();

    const headings = collect({
        subject: 'Subject',
        from: 'From',
        to: 'To',
        status: 'Status',
        created_at: 'Date Created',
    });

    return (
        <Table
            headings={headings}
            body={logs.data.map((log) => (
                <tr key={log.id}>
                    <TableCell>
                        <Link href={route('groups.mail-logs.show', {mail_log: log})} className="text-purple-600 hover:text-purple-800 focus:text-purple-800">
                            <Icon icon={log.uid.startsWith('broadcast') ? 'satellite-dish' : "envelope"} mr />

                            {log.subject}

                            {!!log.has_attachments && (
                                <Icon icon="paperclip" ml />
                            )}
                        </Link>
                    </TableCell>
                    <TableCell>{log.from}</TableCell>
                    <TableCell>{log.to}</TableCell>
                    <TableCell>
                        <MailStatusTag event={log.latest_event} />
                    </TableCell>
                    <TableCell>
                        <DateTag date={log.created_at} />
                    </TableCell>
                </tr>
            ))}
            pagination={<Pagination details={logs} />}
        />
    );
}

export default MailLogTableDesktop;