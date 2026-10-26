import React from 'react';
import TableMobile, { TableMobileHeader, TableMobileLink } from '../../../components/TableMobile';
import BillingTag from './BillingTag';
import Pagination from '../../../components/Pagination';
import Icon from '../../../components/Icon';
import Button from '../../../components/inputs/ButtonV2';
 
const TenantTableMobile = ({ tenants, pagination, hasNonDefaultFilters, setShowFilters }) => {
	const bulkEdit = {
		isActiveMobile: false,
		noun: 'Tenant',
		selectedIds: [],
		totalItems: tenants.length,
	};

	return (
		<div>
			<TableMobileHeader bulkEdit={bulkEdit}>
				<Button
					size="sm"
					intent={hasNonDefaultFilters ? 'success' : 'secondary'}
					weight="clear"
					onClick={() => setShowFilters(previousValue => !previousValue)}
				>
					<Icon icon="filter" mr />
					Filter/Sort
				</Button>
			</TableMobileHeader>
			<TableMobile pagination={<Pagination details={pagination} />}>
				{tenants.map(tenant => (
					<li key={tenant.id} className="flex">
						<TableMobileLink url={route('central.tenants.show', { tenant })}>
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
		</div>
	);
};

export default TenantTableMobile;
