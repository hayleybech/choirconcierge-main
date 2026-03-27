import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import PageHeader from '../../components/PageHeader/PageHeader';
import AppHead from '../../components/AppHead';
import MailingListTableDesktop from './MailingListTableDesktop';
import MailingListTableMobile from './MailingListTableMobile';
import EmptyState from '../../components/EmptyState';
import IndexContainer from '../../components/IndexContainer';
import useRoute from '../../hooks/useRoute';
import TrialAntiSpamNotice from "./TrialAntiSpamNotice";
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';

const Index = ({ lists, can }) => {
	const { route } = useRoute();

	const bulkEdit = useBulkEdit(lists.data, false, can.delete_group, 'List');

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
					bulkEdit.action,
				].filter(action => (action?.can ? can[action.can] : !!action))}
			/>

			<Dialog
				title={`Delete ${bulkEdit.selectedIds.length} Mailing Lists?`}
				isOpen={bulkEdit.showDeleteModal}
				setIsOpen={bulkEdit.setShowDeleteModal}
				okLabel="Delete"
				okVariant="danger-solid"
				okMethod="post"
				data={{ group_ids: bulkEdit.selectedIds }}
				okUrl={route('groups.bulk-destroy')}
				onOk={() => {
					bulkEdit.setSelectedIds([]);
					bulkEdit.setShowDeleteModal(false);
					bulkEdit.setIsForcedMobile(false);
				}}
			>
				Are you sure you want to delete the selected mailing lists? This action cannot be undone.
			</Dialog>

			<BulkEditBar bulkEdit={bulkEdit} />

			<IndexContainer
				tableDesktop={<MailingListTableDesktop lists={lists} bulkEdit={bulkEdit} />}
				tableMobile={<MailingListTableMobile lists={lists} bulkEdit={bulkEdit} />}
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
