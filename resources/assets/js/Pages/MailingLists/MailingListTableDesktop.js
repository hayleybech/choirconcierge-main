import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import {DateTime} from "luxon";
import Table, {TableCell} from "../../components/Table";
import Badge from "../../components/Badge";

const MailingListTableDesktop = ({ tasks }) => (
    <Table
        headings={['Title', 'Type', 'Address', 'Created']}
        body={tasks.map((list) => (
            <tr key={list.id}>
                <TableCell>
                    <div className="flex items-center">
                        <div className="ml-4">
                            <Link href={route('groups.show', list.id)} className="text-sm font-medium text-purple-800">{list.title}</Link>
                        </div>
                    </div>
                </TableCell>
                <TableCell>
                    <i className={`fas fa-fw mr-1.5 ${list.type_icon}`} />{list.list_type.charAt(0).toUpperCase() + list.list_type.slice(1)}
                </TableCell>
                <TableCell>
                    <strong>{ list.email.split('@')[0] }@</strong><span className="text-gray-500">{ list.email.split('@')[1] }</span>
                </TableCell>
                <TableCell>
                    {DateTime.fromJSDate(new Date(list.created_at)).toLocaleString(DateTime.DATE_MED)}
                </TableCell>
            </tr>
        ))}
    />
);

export default MailingListTableDesktop;