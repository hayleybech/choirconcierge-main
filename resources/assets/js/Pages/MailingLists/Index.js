import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import PageHeader from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import MailingListTableDesktop from './MailingListTableDesktop';
import MailingListTableMobile from './MailingListTableMobile';
import { usePage } from '@inertiajs/react';
import EmptyState from '../../components/EmptyState';
import IndexContainer from '../../components/IndexContainer';
import useRoute from '../../hooks/useRoute';
import TenantNotice from '../../components/TenantNotice';
import TrialAntiSpamNotice from "./TrialAntiSpamNotice";

const Index = ({ lists }) => {
	const { can } = usePage().props;
	const { route } = useRoute();

	return (
		<>
			<AppHead title="Mailing Lists" />

			<TrialAntiSpamNotice />

			<PageHeader
				title="Mailing Lists"
				icon="mail-bulk"
				breadcrumbs={[
					{ name: 'Dashboard', url: route('dash') },
					{ name: 'Mailing Lists', url: route('groups.index') },
				]}
				actions={[
					{
						label: 'Add New',
						icon: 'plus',
						url: route('groups.create'),
						variant: 'primary',
						can: 'create_group',
					},
					{
						label: 'Send Broadcast',
						icon: 'inbox-out',
						url: route('groups.broadcasts.create'),
						variant: 'secondary',
						can: 'create_broadcast',
					},
				].filter(action => (action.can ? can[action.can] : true))}
			/>

			<IndexContainer
				tableDesktop={<MailingListTableDesktop lists={lists} />}
				tableMobile={<MailingListTableMobile lists={lists} />}
				emptyState={
					lists.data.length === 0 ? (
						<EmptyState
							title="No mailing lists"
							description="Mailing lists allow you to assign an email address to a group of users, for chat or announcements. "
							actionDescription={
								can['create_group']
									? "You don't have any yet. Get started by adding a mailing list."
									: "You don't have any yet. Ask one of your admins to create one for you."
							}
							icon="mail-bulk"
							href={can['create_group'] ? route('groups.create') : null}
							actionLabel="Add Mailing List"
							actionIcon="plus"
						/>
					) : null
				}
			/>
		</>
	);
};

Index.layout = page => <TenantLayout children={page} />;

export default Index;
