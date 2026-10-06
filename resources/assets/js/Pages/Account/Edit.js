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

const Edit = ({ setSidebarOpen, two_factor_enabled, recovery_codes }) => {
	const { user: authUser } = usePage().props;
	const { route } = useRoute();

	const breadcrumbs = [
		{ name: 'Singers', url: route('singers.index') },
		{ name: authUser.name, url: route('singers.show', { singer: authUser.membership }) },
		{ name: 'Account Settings', url: route('account.edit') },
	];

	return (
		<>
			<AppHead title="Account Settings" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitle>
						<Icon icon="user-edit" type="solid" className="mr-2" />
						Account Settings
					</PageHeaderTitle>
				</PageHeaderContent>
			</PageHeader>

			<AccountForm
				postUrl={route('account.update')}
				cancelUrl={route('singers.show', { singer: authUser.membership })}
				twoFactorUrl={route('account.two-factor.show')}
				twoFactorEnabled={two_factor_enabled}
				recoveryCodes={recovery_codes}
			/>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
