import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell} from "../../components/Table";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";
import collect from "collect.js";
import useRoute from "../../hooks/useRoute";

const MailingListTableDesktop = ({ tasks }) => {
    const { route } = useRoute();

    const headings = collect({
        title: 'Title',
        type: 'Type',
        address: 'Address',
        created: 'Created',
    });

    const list_type_labels = {
      chat: 'Chat',
      public: 'Public',
      distribution: 'Mailout',
    }

    return (
        <Table
            headings={headings}
            body={tasks.map((list) => (
                <tr key={list.id}>
                    <TableCell>
                        <div className="flex items-center">
                            <div className="ml-4">
                                <Link href={route('groups.show', {group: list.id})} className="text-sm font-medium text-purple-800">
                                    {list.title}
                                </Link>
                            </div>
                        </div>
                    </TableCell>
                    <TableCell>
                        <Icon icon={list.type_icon} mr className="text-gray-400" />{list_type_labels[list.list_type]}
                    </TableCell>
                    <TableCell>
                        <strong>{ list.email.split('@')[0] }@</strong><span className="text-gray-500">{ list.email.split('@')[1] }</span>
                    </TableCell>
                    <TableCell>
                        <DateTag date={list.created_at} />
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default MailingListTableDesktop;