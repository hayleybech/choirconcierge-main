import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import PageHeader from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import AccountForm from './AccountForm';
import { usePage } from '@inertiajs/react';
import useRoute from '../../hooks/useRoute';

const Edit = ({}) => {
	const { user: authUser } = usePage().props;
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Edit Profile" />
			<PageHeader
				title="Edit Profile"
				icon="user-edit"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Singers', url: route('singers.index') },
					{ name: authUser.name, url: route('singers.show', { singer: authUser }) },
					{ name: 'Edit Profile', url: route('accounts.edit') },
				]}
			/>

			<AccountForm
				postUrl={route('accounts.update')}
				cancelUrl={route('singers.show', { singer: authUser.membership })}
			/>
		</>
	);
};

Edit.layout = page => <TenantLayout children={page} />;

export default Edit;
