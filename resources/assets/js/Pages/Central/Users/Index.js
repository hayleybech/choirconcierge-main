import React from 'react';

import {
	PageHeader,
	PageHeaderActions,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Icon from '../../../components/Icon';
import Button from '../../../components/inputs/Button';
import AppHead from '../../../components/AppHead';
import IndexContainer from '../../../components/IndexContainer';
import useRoute from '../../../hooks/useRoute';
import CentralLayout from '../../../Layouts/CentralLayout';
import UserTableDesktop from './UserTableDesktop';
import UserTableMobile from './UserTableMobile';
import useSortFilterForm from '../../../hooks/useSortFilterForm';
import useFilterPane from '../../../hooks/useFilterPane';
import FilterSortPane from '../../../components/FilterSortPane';
import Sorts from '../../../components/Sorts';
import UserFilters from './UserFilters';

const Index = ({ users, pagination, setSidebarOpen }) => {
	const { route } = useRoute();

	const [showFilters, setShowFilters, filterAction] = useFilterPane();
	//
	const sorts = [
		{ id: 'full-name', name: 'Name', default: true },
		{ id: 'email', name: 'Email', default: true },
		//     { id: 'created_at', name: 'Date Created' },
	];

	const filters = [{ name: 'search', defaultValue: '' }];

	const sortFilterForm = useSortFilterForm('central.users.index', filters, sorts);

	return (
		<>
			<AppHead title="Users" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation title="Users">
					{filterAction && (
						<PageActionsMenu>
							<ActionMenuItem {...filterAction}>
								<Icon icon={filterAction.icon} mr />
								{filterAction.label}
							</ActionMenuItem>
						</PageActionsMenu>
					)}
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderTitle>
						<Icon icon="users" type="solid" className="mr-2" /> Users
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{filterAction && (
						<Button onClick={filterAction.onClick} size="sm" variant={filterAction.variant}>
							<Icon icon={filterAction.icon} mr />
							{filterAction.label}
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

			<IndexContainer
				showFilters={showFilters}
				filterPane={
					<FilterSortPane
						sorts={<Sorts sorts={sorts} form={sortFilterForm} />}
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
