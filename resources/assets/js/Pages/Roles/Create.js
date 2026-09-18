import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import RoleForm from './RoleForm';
import useRoute from '../../hooks/useRoute';

const Create = ({ setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Roles', url: route('roles.index') },
		{ name: 'Create Role', url: route('roles.create') },
	];

	return (
		<>
			<AppHead title="Create Role" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="user-tag" type="solid" className="mr-2" />
						Create Role
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<RoleForm />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
