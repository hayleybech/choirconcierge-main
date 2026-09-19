import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderTitle,
} from '../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../components/PageTopBar';
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

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: 'Singer Roles', url: route('roles.index') },
	];

	return (
		<>
			<AppHead title="Roles" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
					<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
					{can.create_role && (
						<PageActionsMenu>
							<ActionMenuItem url={route('roles.create')} variant="primary">
								<Icon icon="plus" mr />
								Add New
							</ActionMenuItem>
						</PageActionsMenu>
					)}
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="user-tag" type="solid" className="mr-2" /> Singer Roles
					</PageHeaderTitle>
				</PageHeaderContent>
				<PageHeaderActions>
					{can.create_role && (
						<Button href={route('roles.create')} size="sm" variant="primary">
							<Icon icon="plus" mr />
							Add New
						</Button>
					)}
				</PageHeaderActions>
			</PageHeader>

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
