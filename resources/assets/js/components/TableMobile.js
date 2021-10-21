import React from 'react';
import {Link} from "@inertiajs/inertia-react";
import Icon from "./Icon";

export const TableMobileItem = ({ url, children }) => (
    <li>
        <Link href={url} className="block hover:bg-gray-50">
            <div className="flex items-center px-4 py-4 sm:px-6">
                <div className="flex-1 flex items-center justify-between min-w-0">
                    {children}
                </div>
                <div>
                    <Icon icon="chevron-right" mr className="text-gray-400" />
                </div>
            </div>
        </Link>
    </li>
);

const TableMobile = ({ children }) => (
    <ul className="divide-y divide-gray-200">
        {children}
    </ul>
);

export default TableMobile;