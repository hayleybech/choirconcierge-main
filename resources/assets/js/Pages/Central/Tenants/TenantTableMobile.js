import React from 'react';
import TableMobile, {TableMobileLink} from "../../../components/TableMobile";
import DateTag from "../../../components/DateTag";

const TenantTableMobile = ({ tenants }) => (
    <TableMobile>
        {tenants.map((tenant) => (
            <li key={tenant.id} className="flex pl-4">
                <TableMobileLink url={route('central.tenants.show', {tenant})}>
                    <div className="block hover:bg-gray-50 flex-grow min-w-0 text-gray-500">
                        <div className="flex items-center">
                            <div className="flex-1 flex items-center justify-between min-w-0 w-full">
                                <div className="text-purple-600">{tenant.name}</div>
                                <div>{tenant.renews_at && <DateTag date={tenant.renews_at} />}</div>
                            </div>
                        </div>
                    </div>
                </TableMobileLink>
            </li>
        ))}
    </TableMobile>
);

export default TenantTableMobile;