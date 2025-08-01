import React from 'react';
import TableMobile, {TableMobileLink} from "../../../components/TableMobile";
import useRoute from '../../../hooks/useRoute';
import Icon from '../../../components/Icon';
import MailStatusTag from './MailStatusTag';

const MailLogTableMobile = ({ logs }) => {
    const { route } = useRoute();

    return (
        <TableMobile>
            {logs.map((log) => (
                <li key={log.id} className="flex pl-4">
                    <TableMobileLink url={route('central.mail-logs.show', {mail_log: log})}>
                        <div className="block hover:bg-gray-50 flex-grow min-w-0 text-gray-500">
                            <div className="flex items-center pr-2">
                                <div className="flex-1 flex items-center justify-between min-w-0 w-full gap-2">
                                    <div className="text-purple-600">
                                        <Icon icon={log.uid.startsWith('broadcast') ? 'satellite-dish' : "envelope"} mr />
                                        {log.subject}
                                        {!!log.has_attachments && (
                                            <Icon icon="paperclip" ml />
                                        )}
                                    </div>
                                    <MailStatusTag event={log.latest_event} showLabel={false} />
                                </div>
                            </div>
                        </div>
                    </TableMobileLink>
                </li>
            ))}
        </TableMobile>
    );
}

export default MailLogTableMobile;