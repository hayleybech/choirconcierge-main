import React from 'react';
import TableMobile, {TableMobileLink} from "../../../components/TableMobile";
import DateTag from "../../../components/DateTag";
import BillingTag from "./BillingTag";

const TenantTableMobile = ({ tenants }) => (
    <TableMobile>
        {tenants.map((tenant) => (
            <li key={tenant.id} className="flex pl-4">
                <TableMobileLink url={route('central.tenants.show', {tenant})}>
                    <div className="block hover:bg-gray-50 flex-grow min-w-0 text-gray-500">
                        <div className="flex items-center">
                            <div className="flex-1 flex items-center justify-between min-w-0 w-full gap-2">
                                <div className="text-purple-600">{tenant.name}</div>
                                <BillingTag billing={tenant.billing_status} />
                            </div>
                        </div>
                    </div>
                </TableMobileLink>
            </li>
        ))}
    </TableMobile>
);

export default TenantTableMobile;