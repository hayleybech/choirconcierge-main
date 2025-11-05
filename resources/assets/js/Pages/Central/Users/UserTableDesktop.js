import React from 'react';
import { Link, usePage } from '@inertiajs/react';
import Table, { TableCell } from '../../../components/Table';
import Icon from '../../../components/Icon';
import collect from 'collect.js';
import TableHeadingSort from '../../../components/TableHeadingSort';
import useRoute from '../../../hooks/useRoute';
import Pagination from '../../../components/Pagination';
import ButtonLink from "../../../components/inputs/ButtonLink";

const UserTableDesktop = ({ users, sortFilterForm, pagination }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	const headings = collect({
		name: (
			<TableHeadingSort form={sortFilterForm} sort="full-name">
				Name
			</TableHeadingSort>
		),
		tenants: 'Tenants',
		email: <TableHeadingSort form={sortFilterForm} sort="email">Email</TableHeadingSort>,
	}).filter((item, key) => key !== 'fees' || can['manage_finances']);

	return (
		<>
			<Table
				pagination={<Pagination details={pagination} />}
				headings={headings}
				body={users.map(user => (
					<tr key={user.id}>
						<TableCell>
							<div className="flex items-center">
								<div className="shrink-0 h-10 w-10">
									<img className="h-10 w-10 rounded-md" src={user.avatar_url} alt={user.name} />
								</div>
								<div className="ml-4">
									{/*<Link*/}
									{/*	href={route('central.users.show', { user: user.id })}*/}
									{/*	className="text-sm font-medium text-purple-800"*/}
									{/*>*/}
										{user.name}
									{/*</Link>*/}
									<div>
										<Icon icon="phone" mr className="text-gray-400" />
										{user.phone ? (
											<a href={`tel:${user.phone}`} target="_blank">
												{user.phone}
											</a>
										) : (
											'No phone'
										)}
									</div>
								</div>
							</div>
						</TableCell>
						<TableCell>
							<ul className="flex gap-2">
								{user.memberships.slice(0, 2).map(membership => (
									<li key={membership.id} className="flex gap-2 items-center mb-2">
										<Link
											href={route('central.tenants.show', { tenant: membership.tenant.id })}
											className="text-purple-600 hover:text-purple-500"
										>
											{membership.tenant.name}
										</Link>
										<ButtonLink href={route('dash', {tenant: membership.tenant.id})} size="xs">
											<Icon icon="sign-in-alt" /> Open
										</ButtonLink>
									</li>
								))}
								{user.memberships.length > 3 && (
									<div className="text-sm text-gray-500">...</div>
								)}
							</ul>
						</TableCell>
						<TableCell>
							<Icon icon="envelope" mr className="text-gray-400" />
							<a href={`mailto:${user.email}`} target="_blank">
								{user.email}
							</a>
						</TableCell>
					</tr>
				))}
			/>
		</>
	);
};

export default UserTableDesktop;
