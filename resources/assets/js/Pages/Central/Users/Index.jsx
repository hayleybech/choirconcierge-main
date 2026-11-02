import React from 'react';
 
import {
	PageHeader,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../../components/PageTopBar';
import Icon from '../../../components/Icon';
import AppHead from '../../../components/AppHead';
import IndexContainer from '../../../components/IndexContainer';
import IndexTableControls from '../../../components/IndexTableControls';
import CentralLayout from '../../../Layouts/CentralLayout';
import UserTableDesktop from './UserTableDesktop';
import UserTableMobile from './UserTableMobile';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterSortPane from '../../../components/FilterSortPane';
import UserFilters from './UserFilters';

const Index = ({ users, pagination, setSidebarOpen }) => {
	const [showFilters, setShowFilters] = useFilterPane();
	//
	const sorts = [
		{ id: 'full-name', name: 'Name', default: true },
		{ id: 'email', name: 'Email', default: true },
		//     { id: 'created_at', name: 'Date Created' },
	];

	const filters = [{ name: 'search', label: 'Search', type: 'text', defaultValue: '' }];

	const sortFilterForm = useSortFilterForm('central.users.index', filters, sorts);

	return (
		<>
			<AppHead title="Users" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Users" />
			</PageTopBar>
			<PageHeader hideOnMobile>
				<PageHeaderContent>
					<PageHeaderTitle>
						<Icon icon="users" type="solid" className="mr-2" /> Users
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				tableControls={
					<IndexTableControls
						filters={filters}
						sorts={sorts}
						form={sortFilterForm}
						filterLabel="users"
						onToggleFilters={() => setShowFilters(previousValue => !previousValue)}
					/>
				}
				filterPane={
					<FilterSortPane
						filters={<UserFilters form={sortFilterForm} />}
						closeFn={() => setShowFilters(false)}
					/>
				}
				tableMobile={<UserTableMobile users={users} pagination={pagination} />}
				tableDesktop={
					<UserTableDesktop users={users} sortFilterForm={sortFilterForm} pagination={pagination} />
				}
			/>
		</>
	);
};

Index.layout = page => <CentralLayout children={page} />;

export default Index;
