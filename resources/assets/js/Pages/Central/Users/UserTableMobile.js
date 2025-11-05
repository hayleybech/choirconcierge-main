import React from 'react';
import TableMobile, { TableMobileItem } from '../../../components/TableMobile';
import Icon from '../../../components/Icon';
import useRoute from '../../../hooks/useRoute';
import Pagination from '../../../components/Pagination';
import { Link } from '@inertiajs/react';

const UserTableMobile = ({ users, pagination }) => {
	const { route } = useRoute();

	return (
		<TableMobile pagination={<Pagination details={pagination} />}>
			{users.map(user => (
				<TableMobileItem key={user.id}>
				{/*<TableMobileItem key={user.id} url={route('central.users.show', { singer: user.id })}>*/}
					<div className="shrink-0">
						<img className="h-12 w-12 rounded-lg" src={user.avatar_url} alt={user.name} />
					</div>
					<div className="min-w-0 flex-1 px-4 lg:grid lg:grid-cols-2 lg:gap-4">
						<div>
							<p className="flex items-center min-w-0 mr-1.5">
								{/*<span className="text-sm font-medium text-purple-600 truncate">{user.name}</span>*/}
								<span className="text-sm font-medium text-gray-500 truncate">{user.name}</span>
							</p>
							<div className="flex items-center justify-between">
								<p className="mt-2 hidden sm:flex items-center text-sm text-gray-500 min-w-0">
									<Icon icon="phone" mr className="text-gray-400" />
									<span className="truncate">{user.phone ?? 'No phone'}</span>
								</p>

								<p className="mt-2 items-center text-sm text-gray-500 min-w-0">
									<Icon icon="envelope" mr className="text-gray-400" />
									<span className="truncate">{user.email}</span>
								</p>
							</div>
							<div className="flex flex-col sm:flex-row gap-1 mt-2">
								{user.memberships.slice(0, 2).map(membership => (
									<div key={membership.id}>
										<Link
											href={route('central.tenants.show', { tenant: membership.tenant.id })}
											className="text-purple-600 hover:text-purple-500 text-sm"
										>
											{membership.tenant.name}
										</Link>
									</div>
								))}
								{user.memberships.length > 3 && (
									<div className="text-sm text-gray-500">...</div>
								)}
							</div>
						</div>
					</div>
				</TableMobileItem>
			))}
		</TableMobile>
	);
};

export default UserTableMobile;
