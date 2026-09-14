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
import AccountForm from './AccountForm';
import { usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';

const Edit = ({ setSidebarOpen }) => {
	const { user: authUser } = usePage().props;
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: authUser.name, url: route('singers.show', { singer: authUser }) },
		{ name: 'Edit Profile', url: route('account.edit') },
	];

	return (
		<>
			<AppHead title="Edit Profile" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="user-edit" type="solid" className="mr-2" />
						Edit Profile
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<AccountForm
				postUrl={route('account.update')}
				cancelUrl={route('singers.show', { singer: authUser.membership })}
			/>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
