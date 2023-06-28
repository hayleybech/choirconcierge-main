import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import Icon from "./Icon";

export const TableMobileItem = ({ url, children }) => (
    <TableMobileListItem>
        <TableMobileLink url={url}>
            {children}
        </TableMobileLink>
    </TableMobileListItem>
);

export const TableMobileListItem = ({ children }) => (
    <li className="flex flex-col relative">
        {children}
    </li>
);

export const TableMobileLink = ({ url, children }) => (
    <Link href={url} className="block hover:bg-gray-50 flex-grow min-w-0">
        <div className="flex items-center px-4 py-4 sm:px-6">
            <div className="flex-1 flex items-center justify-between min-w-0 w-full">
                {children}
            </div>
            <div>
                <Icon icon="chevron-right" mr className="text-gray-400" />
            </div>
        </div>
    </Link>
);

const TableMobile = ({ children }) => (
    <ul className="divide-y divide-gray-200">
        {children}
    </ul>
);

export default TableMobile;