import React from 'react';

import { PageHeader, PageHeaderContent, PageHeaderTitle } from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import AppHead from '../../../components/AppHead';
import IndexContainer from '../../../components/IndexContainer';
import IndexTableControls from '../../../components/IndexTableControls';
import TenantTableDesktop from './TenantTableDesktop';
import CentralLayout from '../../../Layouts/CentralLayout';
import TenantTableMobile from './TenantTableMobile';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterDrawer from '../../../components/FilterDrawer';
import Filters from '../../../components/Filters';
import TenantStatsWidget from '../Dash/TenantStatsWidget';
import { createTenantFilterDescriptors } from './TenantFilterDescriptors';

const Index = ({ tenants, pagination, tenantStats, setSidebarOpen }) => {
	const [showFilters, setShowFilters] = useFilterPane();

	const sorts = [
		{ id: 'id', name: 'Organisation Name', default: true },
		{ id: 'created_at', name: 'Date Created' },
	];

	const filters = createTenantFilterDescriptors();

	const sortFilterForm = useSortFilterForm('central.tenants.index', filters, sorts);

	return (
		<>
			<AppHead title="Tenants" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Tenants" />
			</PageTopBar>
			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderTitle>
						<Icon icon="building" type="solid" className="mr-2" /> Tenants
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>
			<IndexContainer
				showFilters={showFilters}
				tableSummary={
					!!tenantStats && (
						<div className="w-full bg-white border-y">
							<TenantStatsWidget {...tenantStats} panel={false} />
						</div>
					)
				}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						filterLabel="tenants"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterDrawer closeFn={() => setShowFilters(false)}>
						<Filters routeName="central.tenants.index" form={sortFilterForm} filters={filters} />
					</FilterDrawer>
				}
				tableMobile={<TenantTableMobile tenants={tenants} pagination={pagination} />}
				tableDesktop={
					<TenantTableDesktop tenants={tenants} sortFilterForm={sortFilterForm} pagination={pagination} />
				}
			/>
		</>
	);
};

Index.layout = page => <CentralLayout children={page} />;

export default Index;
