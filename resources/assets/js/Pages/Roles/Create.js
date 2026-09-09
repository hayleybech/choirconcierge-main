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

const Create = ({ setSidebarOpen }) => {
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Create Role" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('roles.index')}
					breadcrumbs={[
						{ name: 'Singers', url: route('singers.index') },
						{ name: 'Roles', url: route('roles.index') },
					]}
				>
					<PageTopBarTitle title="Create Role" />
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
									{ name: 'Create', url: route('roles.create') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="user-tag" type="solid" className="mr-2" />
							Create Role
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<RoleForm />
		</>
	);
};

Create.layout = page => <TenantLayout children={page} />;

export default Create;
