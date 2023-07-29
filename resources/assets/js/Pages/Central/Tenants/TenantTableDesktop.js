import React from 'react';
import Table, {TableCell} from "../../../components/Table";
import collect from "collect.js";
import DateTag from "../../../components/DateTag";
import {Link} from "@inertiajs/inertia-react";
import ButtonLink from "../../../components/inputs/ButtonLink";
import Icon from "../../../components/Icon";
import useRoute from "../../../hooks/useRoute";

const TenantTableDesktop = ({ tenants }) => {
    const { route } = useRoute();

    const headings = collect({
        name: 'Organisation Name',
        domains: 'Domains',
        timezone: 'Timezone',
        renews_at: 'Fees Due',
        created_at: 'Date Created',
        actions: 'Actions',
    });

    return (
        <Table
            headings={headings}
            body={tenants.map((tenant) => (
                <tr key={tenant.id}>
                    <TableCell>
                        <Link href={route('central.tenants.show', {tenant})} className="text-purple-600 hover:text-purple-800 focus:text-purple-800">
                            {tenant.name}
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
                    <TableCell>
                        <ButtonLink href={route('dash', {tenant})} variant="primary" size="xs">
                          <Icon icon="sign-in-alt" />
                          Open
                        </ButtonLink>
                    </TableCell>
                </tr>
            ))}
        />
    );
}

export default TenantTableDesktop;