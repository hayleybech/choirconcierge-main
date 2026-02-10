import React from 'react';
import Table, { TableCell } from '../../../components/Table';
import collect from 'collect.js';
import DateTag from '../../../components/DateTag';
import { Link } from '@inertiajs/react';
import ButtonLink from '../../../components/inputs/ButtonLink';
import Icon from '../../../components/Icon';
import useRoute from '../../../hooks/useRoute';
import BillingTag from './BillingTag';
import TableHeadingSort from '../../../components/TableHeadingSort';
import Pagination from '../../../components/Pagination';
import DomainTag from '../../../components/DomainTag';

const TenantTableDesktop = ({ tenants, sortFilterForm, pagination }) => {
	const { route } = useRoute();

	const headings = collect({
		name: (
			<TableHeadingSort form={sortFilterForm} sort="id">
				Organisation Name
			</TableHeadingSort>
		),
		domains: 'Domains',
		timezone: 'Timezone',
		renews_at: 'Billing',
		created_at: (
			<TableHeadingSort form={sortFilterForm} sort="created_at">
				Date Created
			</TableHeadingSort>
		),
		actions: 'Actions',
	});

	return (
		<Table
			pagination={<Pagination details={pagination} />}
			headings={headings}
			body={tenants.map(tenant => (
				<tr key={tenant.id}>
					<TableCell>
						<Link
							href={route('central.tenants.show', { tenant })}
							className="text-purple-600 hover:text-purple-800 focus:text-purple-800"
						>
							{tenant.name}
						</Link>
					</TableCell>
					<TableCell>
						<div className="flex gap-1 flex-wrap">
							{tenant.domains.map(domainItem => (
								<DomainTag>{domainItem.domain}</DomainTag>
							))}
						</div>
					</TableCell>
					<TableCell>{tenant.timezone}</TableCell>
					<TableCell>
						<BillingTag billing={tenant.billing_status} />
					</TableCell>
					<TableCell>
						<DateTag icon="pencil" date={tenant.created_at} />
					</TableCell>
					<TableCell>
						<ButtonLink href={route('dash', { tenant })} variant="primary" size="xs">
							<Icon icon="sign-in-alt" />
							Open
						</ButtonLink>
					</TableCell>
				</tr>
			))}
		/>
	);
};

export default TenantTableDesktop;
