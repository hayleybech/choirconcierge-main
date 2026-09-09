import React from 'react';
import TenantLayout from '../../Layouts/TenantLayout';
import { PageHeader2 } from '../../components/PageHeader/PageHeader';
import { PageHeading } from '../../components/PageHeader/PageHeading';
import PageTopBar, { PageActionsMenu, PageTopBarTitle } from '../../components/PageTopBar';
import ActionMenuItem from '../../components/ActionMenu/ActionMenuItem';
import Button from '../../components/inputs/Button';
import Icon from '../../components/Icon';
import AppHead from '../../components/AppHead';
import MailingListTableDesktop from './MailingListTableDesktop';
import MailingListTableMobile from './MailingListTableMobile';
import EmptyState from '../../components/EmptyState';
import IndexContainer from '../../components/IndexContainer';
import useRoute from '../../hooks/useRoute';
import TrialAntiSpamNotice from './TrialAntiSpamNotice';
import useBulkEdit from '../../hooks/useBulkEdit';
import Dialog from '../../components/Dialog';
import BulkEditBar from '../../components/BulkEditBar';

const Index = ({ lists, can, setSidebarOpen }) => {
	const { route } = useRoute();

	const bulkEdit = useBulkEdit(lists.data, false, can.delete_group, 'List');
	const actions = [
		{ label: 'Add New', icon: 'plus', url: route('groups.create'), variant: 'primary', can: 'create_group' },
		{
			label: 'Send Broadcast',
			icon: 'inbox-out',
			url: route('communications.create'),
			variant: 'secondary',
			can: 'create_broadcast',
		},
		bulkEdit.action,
	].filter(action => (action?.can ? can[action.can] : !!action));

	return (
		<>
			<AppHead title="Mailing Lists" />

			<TrialAntiSpamNotice />

			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<div className="flex justify-between grow">
					<PageTopBarTitle title="Mailing Lists" />
					<PageActionsMenu>
						{actions.map(action => (
							<ActionMenuItem
								key={action.label}
								url={action.url}
								onClick={action.onClick}
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</ActionMenuItem>
						))}
					</PageActionsMenu>
				</div>
			</PageTopBar>
			<PageHeader2>
				<div className="lg:flex lg:items-center lg:justify-between">
					<PageHeading>
						<Icon icon="mail-bulk" type="solid" className="mr-2" />
						Mailing Lists
					</PageHeading>

					<div className="hidden lg:flex mt-0 lg:ml-4 gap-3">
						{actions.map(action => (
							<Button
								key={action.label}
								href={action.url}
								onClick={action.onClick}
								size="sm"
								variant={action.variant}
							>
								<Icon icon={action.icon} mr />
								{action.label}
							</Button>
						))}
					</div>
				</div>
			</PageHeader2>

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
