import React from 'react';
import PageHeader from '../../../components/PageHeader';
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

const Index = ({ users, pagination }) => {
	const { route } = useRoute();

	const [showFilters, setShowFilters, filterAction, hasNonDefaultFilters] = useFilterPane();
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
			<PageHeader
				title="Users"
				icon="users"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('central.dash') },
					{ name: 'Users', url: route('central.users.index') },
				]}
				actions={[filterAction]}
				optionsVariant={hasNonDefaultFilters ? 'success-solid' : 'secondary'}
			/>

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
