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

const Edit = ({ role, setSidebarOpen }) => {
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Roles', url: route('roles.index') },
		{ name: role.name, url: route('roles.show', { role }) },
		{ name: 'Edit Singer Role', url: route('roles.edit', { role }) },
	];

	return (
		<>
			<AppHead title={`Edit - ${role.name}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="user-tag" type="solid" className="mr-2" />
						Edit Singer Role
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<RoleForm role={role} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
