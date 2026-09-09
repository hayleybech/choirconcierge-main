import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Icon from '../../components/Icon';
import Button from '../../components/inputs/Button';
import AppHead from '../../components/AppHead';
import RoleTableDesktop from './RoleTableDesktop';
import RoleTableMobile from './RoleTableMobile';
import { usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';

const Index = ({ roles, setSidebarOpen }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Roles" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopNavigation
						backUrl={route('singers.index')}
						breadcrumbs={[{ name: 'Singers', url: route('singers.index') }]}
					>
						<PageTopBarTitle title="Singer Roles" />
					</PageTopNavigation>
					{can.create_role && (
						<PageActionsMenu>
							<ActionMenuItem url={route('roles.create')} variant="primary">
								<Icon icon="plus" mr />
								Add New
							</ActionMenuItem>
						</PageActionsMenu>
					)}
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Singers', url: route('singers.index') },
									{ name: 'Roles', url: route('roles.index') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="user-tag" type="solid" className="mr-2" /> Singer Roles
						</PageHeading>
					</div>
					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{can.create_role && (
							<Button href={route('roles.create')} size="sm" variant="primary">
								<Icon icon="plus" mr />
								Add New
							</Button>
						)}
					</div>
				</div>
			</PageHeader2>

			{/* Desktop Table */}
			<div className="hidden lg:flex flex-col">
				<RoleTableDesktop roles={roles} />
			</div>

			{/* Mobile Table */}
			<div className="bg-white shadow block lg:hidden">
				<RoleTableMobile roles={roles} />
			</div>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
