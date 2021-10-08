import React from 'react';
import {Link} from "@inertiajs/inertia-react";

export const TableMobileItem = ({ url, children }) => (
    <li>
        <Link href={url} className="block hover:bg-gray-50">
            <div className="flex items-center px-4 py-4 sm:px-6">
                <div className="flex-1 flex items-center justify-between min-w-0">
                    {children}
                </div>
                <div>
                    <i className="fa fa-fw fa-chevron-right text-gray-400" />
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