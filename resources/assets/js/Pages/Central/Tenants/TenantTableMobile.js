import React from 'react';
import TableMobile from "../../../components/TableMobile";
import DateTag from "../../../components/DateTag";

const TenantTableMobile = ({ tenants }) => (
    <TableMobile>
        {tenants.map((tenant) => (
            <li key={tenant.id} className="flex pl-4">
                <div className="block hover:bg-gray-50 flex-grow min-w-0 text-gray-500">
                    <div className="flex items-center px-4 py-4 sm:px-6">
                        <div className="flex-1 flex items-center justify-between min-w-0 w-full">
                            <div>{tenant.choir_name}</div>
                            <div>{tenant.renews_at && <DateTag date={tenant.renews_at} />}</div>
                        </div>
                    </div>
                </div>
            </li>
        ))}
    </TableMobile>
);

export default TenantTableMobile;