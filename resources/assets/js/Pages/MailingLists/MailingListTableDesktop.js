import React from 'react';
import {Link} from "@inertiajs/react";
import Table, {TableCell, THead, TBody, TableHeading} from "../../components/Table";
import Icon from "../../components/Icon";
import DateTag from "../../components/DateTag";
import useRoute from "../../hooks/useRoute";
import Pagination from '../../components/Pagination';

const MailingListTableDesktop = ({ lists }) => {
    const { route } = useRoute();

    const list_type_labels = {
      chat: 'Chat',
      public: 'Public',
      distribution: 'Mailout',
    }

    return (
        <Table pagination={<Pagination details={lists} />}>
            <THead>
                <tr>
                    <TableHeading>Title</TableHeading>
                    <TableHeading>Type</TableHeading>
                    <TableHeading>Address</TableHeading>
                    <TableHeading>Created</TableHeading>
                </tr>
            </THead>
            <TBody>
                {lists.data.map((list) => (
                    <tr key={list.id}>
                        <TableCell>
                            <Link href={route('groups.show', {group: list.id})} className="text-sm font-medium text-purple-800">
                                {list.title}
                            </Link>
                        </TableCell>
                        <TableCell>
                            <Icon icon={list.type_icon} mr className="text-gray-400" />{list_type_labels[list.list_type]}
                        </TableCell>
                        <TableCell>
                            <strong>{ list.email.split('@')[0] }@</strong><span className="text-gray-500">{ list.email.split('@')[1] }</span>
                        </TableCell>
                        <TableCell>
                            <DateTag icon="pencil" date={list.created_at} />
                        </TableCell>
                    </tr>
                ))}
            </TBody>
        </Table>
    );
}

export default MailingListTableDesktop;