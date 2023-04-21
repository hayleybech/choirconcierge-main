import React from 'react';
import Table, {TableCell} from "../../../components/Table";
import collect from "collect.js";
import DateTag from "../../../components/DateTag";
import {Link} from "@inertiajs/react";

const TenantTableDesktop = ({ tenants }) => {
    const headings = collect({
        choir_name: 'Choir Name',
        domains: 'Domains',
        timezone: 'Timezone',
        renews_at: 'Fees Due',
        created_at: 'Date Created',
    });

    return (
        <Table
            headings={headings}
            body={tenants.map((tenant) => (
                <tr key={tenant.id}>
                    <TableCell>
                        <Link href={route('central.tenants.show', {tenant})} className="text-purple-600 hover:text-purple-800 focus:text-purple-800">
                            {tenant.choir_name}
                        </Link>
                    </TableCell>
                    <TableCell>{tenant.domains.map(domainItem => domainItem.domain).join()}</TableCell>
                    <TableCell>{tenant.timezone.timezone}</TableCell>
                    <TableCell>
                        {tenant.renews_at && <DateTag date={tenant.renews_at} />}
                    </TableCell>
                    <TableCell>
                        <DateTag date={tenant.created_at} />
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default TenantTableDesktop;