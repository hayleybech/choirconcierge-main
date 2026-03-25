import React from 'react';
import Table, { TableCell, THead, TBody, TableHeading } from '../../../components/Table';
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
			<TableHeading>
				<TableHeadingSort form={sortFilterForm} sort="id">
					Organisation Name
				</TableHeadingSort>
			</TableHeading>
		),
		domains: <TableHeading>Domains</TableHeading>,
		timezone: <TableHeading>Timezone</TableHeading>,
		renews_at: <TableHeading>Billing</TableHeading>,
		created_at: (
			<TableHeading>
				<TableHeadingSort form={sortFilterForm} sort="created_at">
					Date Created
				</TableHeadingSort>
			</TableHeading>
		),
		actions: <TableHeading>Actions</TableHeading>,
	});

	return (
		<Table pagination={<Pagination details={pagination} />}>
			<THead>
				<tr>{headings.values().toArray()}</tr>
			</THead>
			<TBody>
				{tenants.map(tenant => (
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
									<DomainTag key={domainItem.domain}>{domainItem.domain}</DomainTag>
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
			</TBody>
		</Table>
	);
};

export default TenantTableDesktop;
