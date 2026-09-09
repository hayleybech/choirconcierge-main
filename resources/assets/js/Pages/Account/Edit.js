import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import Breadcrumbs from '../../components/PageHeader/Breadcrumbs';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageTopBarTitle, PageTopNavigation } from '../../components/PageTopBar';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import AccountForm from './AccountForm';
import { usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';

const Edit = ({ setSidebarOpen }) => {
	const { user: authUser } = usePage().props;
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Edit Profile" />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation
					backUrl={route('singers.show', { singer: authUser.membership })}
					breadcrumbs={[
						{ name: 'Singers', url: route('singers.index') },
						{ name: authUser.name, url: route('singers.show', { singer: authUser }) },
					]}
				>
					<PageTopBarTitle title="Edit Profile" />
				</PageTopNavigation>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<div className="flex-1 min-w-0">
						<div className="hidden lg:block">
							<Breadcrumbs
								breadcrumbs={[
									{ name: 'Singers', url: route('singers.index') },
									{ name: authUser.name, url: route('singers.show', { singer: authUser }) },
									{ name: 'Edit Profile', url: route('account.edit') },
								]}
								showLastChevron={false}
							/>
						</div>
						<PageHeading>
							<Icon icon="user-edit" type="solid" className="mr-2" />
							Edit Profile
						</PageHeading>
					</div>
				</div>
			</PageHeader2>

			<AccountForm
				postUrl={route('account.update')}
				cancelUrl={route('singers.show', { singer: authUser.membership })}
			/>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
