import React, { useState } from 'react';
import AppHead from '../../../components/AppHead';
import {
	PageHeader,
	PageHeaderActions,
	PageHeaderBreadcrumbs,
	PageHeaderContent,
	PageHeaderMeta,
	PageHeaderTitle,
	PageHeaderTitleRow,
} from '../../../components/PageHeader/PageHeader';
import PageTopBar, { PageActionsMenu, PageTopNavigation } from '../../../components/PageTopBar';
import ActionMenuItem from '../../../components/ActionMenu/ActionMenuItem';
import Button from '../../../components/inputs/ButtonV2';
import Icon from '../../../components/Icon';
import DateTag from '../../../components/DateTag';
import SectionHeader from '../../../components/SectionHeader';
import SectionTitle from '../../../components/SectionTitle';
import TenantLayout from '../../../Layouts/TenantLayout';
import DeleteDialog from '../../../components/DeleteDialog';
import Prose from '../../../components/Prose';
import useRoute from '../../../hooks/useRoute';
 
const Show = ({ task, notification, setSidebarOpen }) => {
	const { route } = useRoute();

	const [deleteDialogIsOpen, setDeleteDialogIsOpen] = useState(false);
	const actions = [
		{
			label: 'Edit',
			icon: 'edit',
			href: route('tasks.notifications.edit', { task, notification }),
			can: task.can.update_task,
			weight: 'outline',
		},
		{
			label: 'Delete',
			icon: 'trash',
			onClick: () => setDeleteDialogIsOpen(true),
			intent: 'danger',
			can: task.can.delete_task,
		},
	].filter(action => action.can);

	const breadcrumbs = [
		{ name: 'Onboarding', url: route('tasks.index') },
		{ name: task.name, url: route('tasks.show', { task }) },
		{
			name: notification.subject,
			url: route('tasks.notifications.show', { task, notification }),
		},
	];

	return (
		<>
			<AppHead title={`${notification.subject} - Task Notifications for "${task.name}"`} />
			<PageTopBar setSidebarOpen={setSidebarOpen}>
				<PageTopNavigation breadcrumbs={breadcrumbs}></PageTopNavigation>
				<PageActionsMenu>
					{actions.map(action => (
						<ActionMenuItem
							key={action.label}
							url={action.href}
							onClick={action.onClick}
							intent={action.intent}
						>
							<Icon icon={action.icon} mr />
							{action.label}
						</ActionMenuItem>
					))}
				</PageActionsMenu>
			</PageTopBar>
			<PageHeader>
				<PageHeaderContent>
					<PageHeaderBreadcrumbs breadcrumbs={breadcrumbs} />
					<PageHeaderTitleRow>
						<PageHeaderTitle>
							<Icon icon="bell" type="solid" className="mr-2" />
							{notification.subject}
						</PageHeaderTitle>
						<PageHeaderActions>
							{actions.map(action => (
								<Button
									key={action.label}
									{...action}
									size="md"
									weight={action.weight ?? 'clear'}
								>
									<Icon icon={action.icon} mr />
									{action.label}
								</Button>
							))}
						</PageHeaderActions>
					</PageHeaderTitleRow>
					<PageHeaderMeta>
						<div>Recipients: {notification.recipients}</div>
						<div>Delay: {notification.delay}</div>
						<DateTag icon="pencil" date={notification.created_at} label="Created" />
						<DateTag icon="pencil" date={notification.updated_at} label="Updated" />
					</PageHeaderMeta>
				</PageHeaderContent>
			</PageHeader>

			<DeleteDialog
				title="Delete Notifications"
				url={route('tasks.notifications.destroy', { task, notification })}
				isOpen={deleteDialogIsOpen}
				setIsOpen={setDeleteDialogIsOpen}
			>
				Are you sure you want to delete this task notification? This may break your onboarding process! This
				action cannot be undone.
			</DeleteDialog>

			<div className="py-6 px-4 sm:px-6 lg:px-8">
				<SectionHeader>
					<SectionTitle>Body</SectionTitle>
				</SectionHeader>

				<Prose content={notification.body} className="mb-8" />
			</div>
		</>
	);
};

Show.layout = page => <TenantLayout children={page} />;

export default Show;
