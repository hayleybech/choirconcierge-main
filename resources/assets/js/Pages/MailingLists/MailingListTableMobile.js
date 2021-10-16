import React from 'react';
import TableMobile, {TableMobileItem} from "../../components/TableMobile";
import Badge from "../../components/Badge";
import {DateTime} from "luxon";

const MailingListTableMobile = ({ lists }) => (
    <TableMobile>
        {lists.map((list) => (
            <TableMobileItem key={list.id} url={route('groups.show', list.id)}>
                <div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
                    <div className="flex items-center justify-between">
                        <p className="flex items-center min-w-0 mr-1.5">
                            <span className="text-sm font-medium text-purple-600 truncate">{list.title}</span>
                        </p>
                        <div className="text-xs text-gray-500 flex-shrink-0">
                            <i className={`fas fa-fw mr-1.5 ${list.type_icon}`} />{list.list_type.charAt(0).toUpperCase() + list.list_type.slice(1)}
                        </div>
                    </div>
                    <div className="flex items-center justify-between">
                        <p className="mt-1.5 flex items-center text-xs text-gray-500 min-w-0">
                            <i className="fas fa-fw fa-envelope mr-1.5" />
                            <strong>{ list.email.split('@')[0] }@</strong><span className="text-gray-500 hidden sm:flex">{ list.email.split('@')[1] }</span>
                        </p>
                    </div>
                </div>
            </TableMobileItem>
        ))}
    </TableMobile>
);

export default MailingListTableMobile;