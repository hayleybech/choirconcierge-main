import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import RoleForm from './RoleForm';
import useRoute from '../../hooks/useRoute';

const Edit = ({ role, setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title={`Edit - ${role.name}`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('roles.show', { role })}
					breadcrumbs={[
						{ name: 'Singers', url: route('singers.index') },
						{ name: 'Roles', url: route('roles.index') },
						{ name: role.name, url: route('roles.show', { role }) },
					]}
				>
					<PageTopBarTitle title="Edit Singer Role" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Singers', url: route('singers.index') },
									{ name: 'Roles', url: route('roles.index') },
									{ name: role.name, url: route('roles.show', { role }) },
									{ name: 'Edit', url: route('roles.edit', { role }) },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="user-tag" type="solid" className="mr-2" />
							Edit Singer Role
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<RoleForm role={role} />
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
