import React from 'react';
import Table, {TableCell} from "../../../components/Table";
import collect from "collect.js";
import DateTag from "../../../components/DateTag";

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
                        <div className="flex items-center">
                            <div className="ml-4">{tenant.choir_name}</div>
                        </div>
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